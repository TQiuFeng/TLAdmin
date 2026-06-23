<?php

namespace app\common\service\security;

use app\common\config\ConfigRepository;
use app\common\http\Request;
use app\common\service\geo\IpLocationService;

/**
 * IP 归属地屏蔽服务。
 *
 * 基于本地 IP 归属地库判断访问来源，支持黑名单/白名单、归属地、运营商、CIDR 和 IP 段规则。
 * Author: qiufeng
 */
final class IpBlockService
{
    private const MODE_BLACKLIST = 'blacklist';
    private const MODE_WHITELIST = 'whitelist';

    public function __construct(
        private readonly ConfigRepository $config,
        private readonly IpLocationService $ipLocationService
    ) {
    }

    public function config(): array
    {
        return $this->normalizeConfig((array) $this->config->get('security.ip_block', []));
    }

    public function save(array $payload): array
    {
        $current = $this->config();
        $config = $this->normalizeConfig([
            'enabled' => $payload['enabled'] ?? $current['enabled'],
            'mode' => $payload['mode'] ?? $current['mode'],
            'trust_proxy_headers' => $payload['trust_proxy_headers'] ?? $current['trust_proxy_headers'],
            'excluded_paths' => $payload['excluded_paths'] ?? $current['excluded_paths'],
            'rules' => $payload['rules'] ?? $current['rules'],
        ]);

        $this->config->set('security.ip_block', $config);
        $this->config->save();

        return $config;
    }

    public function evaluateRequest(Request $request): array
    {
        $config = $this->config();
        $ip = $request->clientIp((bool) $config['trust_proxy_headers']);

        if ($this->isPathExcluded($request->path(), $config['excluded_paths'])) {
            return [
                'enabled' => (bool) $config['enabled'],
                'blocked' => false,
                'ip' => $ip,
                'path' => $request->path(),
                'mode' => $config['mode'],
                'skipped' => true,
                'reason' => '当前路径已排除屏蔽检查',
                'location' => null,
                'matched_rule' => null,
            ];
        }

        return array_replace($this->evaluateIp($ip, $config), [
            'path' => $request->path(),
            'skipped' => false,
        ]);
    }

    public function evaluateIp(string $ip, ?array $config = null): array
    {
        $config = $this->normalizeConfig($config ?? $this->config());
        $location = $this->ipLocationService->lookup($ip);

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return [
                'enabled' => (bool) $config['enabled'],
                'blocked' => (bool) $config['enabled'],
                'ip' => $ip,
                'mode' => $config['mode'],
                'reason' => '仅支持 IPv4 地址',
                'location' => $location,
                'matched_rule' => null,
            ];
        }

        if (!$config['enabled']) {
            return [
                'enabled' => false,
                'blocked' => false,
                'ip' => $ip,
                'mode' => $config['mode'],
                'reason' => 'IP 归属地屏蔽未启用',
                'location' => $location,
                'matched_rule' => null,
            ];
        }

        $matchedRule = $this->firstMatchedRule($ip, $location, $config['rules']);
        $matched = $matchedRule !== null;
        $blocked = $config['mode'] === self::MODE_WHITELIST ? !$matched : $matched;

        return [
            'enabled' => true,
            'blocked' => $blocked,
            'ip' => $ip,
            'mode' => $config['mode'],
            'reason' => $this->reason($config['mode'], $blocked, $matched),
            'location' => $location,
            'matched_rule' => $matchedRule,
        ];
    }

    private function normalizeConfig(array $config): array
    {
        $mode = (string) ($config['mode'] ?? self::MODE_BLACKLIST);
        if (!in_array($mode, [self::MODE_BLACKLIST, self::MODE_WHITELIST], true)) {
            $mode = self::MODE_BLACKLIST;
        }

        return [
            'enabled' => filter_var($config['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'mode' => $mode,
            'trust_proxy_headers' => filter_var($config['trust_proxy_headers'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'excluded_paths' => $this->normalizeStringList($config['excluded_paths'] ?? []),
            'rules' => $this->normalizeRules($config['rules'] ?? []),
        ];
    }

    private function normalizeRules(mixed $rules): array
    {
        if (!is_array($rules)) {
            return [];
        }

        $normalized = [];
        foreach ($rules as $index => $rule) {
            if (!is_array($rule)) {
                continue;
            }

            $item = [
                'id' => $this->stringValue($rule['id'] ?? ('rule_' . ($index + 1))),
                'name' => $this->stringValue($rule['name'] ?? ''),
                'enabled' => filter_var($rule['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'country' => $this->stringValue($rule['country'] ?? ''),
                'province' => $this->stringValue($rule['province'] ?? ''),
                'city' => $this->stringValue($rule['city'] ?? ''),
                'isp' => $this->stringValue($rule['isp'] ?? ''),
                'ip' => $this->stringValue($rule['ip'] ?? ''),
                'cidr' => $this->stringValue($rule['cidr'] ?? ''),
                'start_ip' => $this->stringValue($rule['start_ip'] ?? ''),
                'end_ip' => $this->stringValue($rule['end_ip'] ?? ''),
                'remark' => $this->stringValue($rule['remark'] ?? ''),
            ];

            if (!$this->hasAnyCondition($item)) {
                throw new \InvalidArgumentException('IP 屏蔽规则必须至少填写一个条件');
            }

            $this->assertRuleIpFields($item);
            $normalized[] = $item;
        }

        return $normalized;
    }

    private function normalizeStringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $items[] = $item;
            }
        }

        return array_values(array_unique($items));
    }

    private function firstMatchedRule(string $ip, array $location, array $rules): ?array
    {
        foreach ($rules as $rule) {
            if (!$rule['enabled']) {
                continue;
            }

            if ($this->matchesRule($ip, $location, $rule)) {
                return $rule;
            }
        }

        return null;
    }

    private function matchesRule(string $ip, array $location, array $rule): bool
    {
        foreach (['country', 'province', 'city', 'isp'] as $field) {
            if ($rule[$field] !== '' && $rule[$field] !== (string) ($location[$field] ?? '')) {
                return false;
            }
        }

        if ($rule['ip'] !== '' && $rule['ip'] !== $ip) {
            return false;
        }

        if ($rule['cidr'] !== '' && !$this->ipInCidr($ip, $rule['cidr'])) {
            return false;
        }

        if ($rule['start_ip'] !== '' || $rule['end_ip'] !== '') {
            if ($rule['start_ip'] === '' || $rule['end_ip'] === '') {
                return false;
            }

            $target = $this->ipToUnsignedLong($ip);
            if ($target < $this->ipToUnsignedLong($rule['start_ip']) || $target > $this->ipToUnsignedLong($rule['end_ip'])) {
                return false;
            }
        }

        return true;
    }

    private function isPathExcluded(string $path, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if ($pattern === $path) {
                return true;
            }

            if (str_ends_with($pattern, '*') && str_starts_with($path, rtrim($pattern, '*'))) {
                return true;
            }
        }

        return false;
    }

    private function reason(string $mode, bool $blocked, bool $matched): string
    {
        if ($mode === self::MODE_WHITELIST) {
            return $blocked ? '白名单模式未命中允许规则' : '白名单模式已命中允许规则';
        }

        return $blocked ? '黑名单模式已命中屏蔽规则' : ($matched ? '黑名单模式命中但未屏蔽' : '黑名单模式未命中屏蔽规则');
    }

    private function hasAnyCondition(array $rule): bool
    {
        foreach (['country', 'province', 'city', 'isp', 'ip', 'cidr', 'start_ip', 'end_ip'] as $field) {
            if ($rule[$field] !== '') {
                return true;
            }
        }

        return false;
    }

    private function assertRuleIpFields(array $rule): void
    {
        foreach (['ip', 'start_ip', 'end_ip'] as $field) {
            if ($rule[$field] !== '' && !filter_var($rule[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                throw new \InvalidArgumentException("{$field} 必须是合法 IPv4 地址");
            }
        }

        if ($rule['cidr'] !== '' && !$this->isValidCidr($rule['cidr'])) {
            throw new \InvalidArgumentException('cidr 必须是合法 IPv4 CIDR，例如 223.5.5.0/24');
        }

        if ($rule['start_ip'] !== '' && $rule['end_ip'] !== '') {
            if ($this->ipToUnsignedLong($rule['start_ip']) > $this->ipToUnsignedLong($rule['end_ip'])) {
                throw new \InvalidArgumentException('start_ip 不能大于 end_ip');
            }
        }
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr, 2);
        $bits = (int) $bits;
        $mask = $bits === 0 ? 0 : ((0xFFFFFFFF << (32 - $bits)) & 0xFFFFFFFF);

        return ($this->ipToUnsignedLong($ip) & $mask) === ($this->ipToUnsignedLong($subnet) & $mask);
    }

    private function isValidCidr(string $cidr): bool
    {
        if (!str_contains($cidr, '/')) {
            return false;
        }

        [$ip, $bits] = explode('/', $cidr, 2);

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
            && ctype_digit($bits)
            && (int) $bits >= 0
            && (int) $bits <= 32;
    }

    private function ipToUnsignedLong(string $ip): int
    {
        $value = ip2long($ip);
        if ($value === false) {
            throw new \InvalidArgumentException('IP 地址格式错误');
        }

        return (int) sprintf('%u', $value);
    }

    private function stringValue(mixed $value): string
    {
        return trim((string) $value);
    }
}
