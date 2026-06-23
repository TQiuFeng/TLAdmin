<?php

namespace app\common\service\integration;

use app\common\config\ConfigRepository;
use app\common\support\Tools;

/**
 * 第三方能力配置服务。
 *
 * 负责支付、微信、短信、存储配置的 schema 输出、保存、密钥加密、脱敏展示和 Composer 包状态检查。
 * Author: qiufeng
 */
final class IntegrationConfigService
{
    private const GROUPS = ['pay', 'wechat', 'wechat_miniapp', 'sms', 'storage'];
    private const ENCRYPT_PREFIX = 'enc:v1:';
    private const MASK_VALUE = '******';

    public function __construct(private readonly ConfigRepository $config)
    {
    }

    public function list(): array
    {
        $items = [];
        foreach (self::GROUPS as $group) {
            $items[] = $this->detail($group);
        }

        return $items;
    }

    public function detail(string $group): array
    {
        $this->assertGroup($group);

        return [
            'group' => $group,
            'schema' => $this->config->get("integration.schema.{$group}", []),
            'config' => $this->maskedConfig($group),
            'packages' => $this->packageStatus($group),
        ];
    }

    public function save(string $group, array $payload): array
    {
        $this->assertGroup($group);

        $current = $this->config->get("integration.config.{$group}", []);
        $schema = $this->config->get("integration.schema.{$group}", []);

        foreach ($schema as $field) {
            $name = (string) ($field['field'] ?? '');
            if ($name === '' || !array_key_exists($name, $payload)) {
                continue;
            }

            $value = $payload[$name];
            if (($field['secret'] ?? false) === true) {
                if ($value === '' || $value === self::MASK_VALUE) {
                    continue;
                }
                $value = $this->encryptSecret((string) $value);
            }

            $this->setNested($current, $name, $value);
        }

        $this->config->set("integration.config.{$group}", $current);
        $this->config->save();

        return $this->detail($group);
    }

    public function rawConfig(string $group): array
    {
        $this->assertGroup($group);

        return $this->decryptSecrets(
            $this->config->get("integration.config.{$group}", []),
            $this->config->get("integration.schema.{$group}", [])
        );
    }

    private function maskedConfig(string $group): array
    {
        $config = $this->config->get("integration.config.{$group}", []);
        $schema = $this->config->get("integration.schema.{$group}", []);

        foreach ($schema as $field) {
            if (($field['secret'] ?? false) !== true) {
                continue;
            }

            $name = (string) ($field['field'] ?? '');
            $value = Tools::arrGet($config, $name);
            if (is_string($value) && $value !== '') {
                $this->setNested($config, $name, self::MASK_VALUE);
            }
        }

        return $config;
    }

    private function decryptSecrets(array $config, array $schema): array
    {
        foreach ($schema as $field) {
            if (($field['secret'] ?? false) !== true) {
                continue;
            }

            $name = (string) ($field['field'] ?? '');
            $value = Tools::arrGet($config, $name);
            if (is_string($value) && str_starts_with($value, self::ENCRYPT_PREFIX)) {
                $this->setNested($config, $name, Tools::decrypt(substr($value, strlen(self::ENCRYPT_PREFIX)), $this->secretKey()));
            }
        }

        return $config;
    }

    private function packageStatus(string $group): array
    {
        $map = [
            'pay' => ['pay'],
            'wechat' => ['wechat'],
            'wechat_miniapp' => ['wechat_miniapp'],
            'sms' => ['sms'],
            'storage' => ['filesystem', 'aliyun_oss', 'tencent_cos', 'qiniu'],
        ];

        $packages = [];
        foreach ($map[$group] ?? [] as $key) {
            $package = $this->config->get("integration.packages.{$key}", []);
            $class = (string) ($package['class'] ?? '');
            $packages[] = [
                'key' => $key,
                'name' => $package['name'] ?? $key,
                'title' => $package['title'] ?? $key,
                'class' => $class,
                'installed' => $class !== '' && class_exists($class),
            ];
        }

        return $packages;
    }

    private function encryptSecret(string $value): string
    {
        return self::ENCRYPT_PREFIX . Tools::encrypt($value, $this->secretKey());
    }

    /**
     * 开发环境允许使用兜底 key，生产环境必须配置 APP_KEY。
     */
    private function secretKey(): string
    {
        return (string) ($this->config->get('app.key') ?: getenv('APP_KEY') ?: 'tladmin-local-dev-key');
    }

    private function setNested(array &$array, string $key, mixed $value): void
    {
        $segments = explode('.', $key);
        $cursor = &$array;

        foreach ($segments as $segment) {
            if (!isset($cursor[$segment]) || !is_array($cursor[$segment])) {
                $cursor[$segment] = [];
            }
            $cursor = &$cursor[$segment];
        }

        $cursor = $value;
    }

    private function assertGroup(string $group): void
    {
        if (!in_array($group, self::GROUPS, true)) {
            throw new \InvalidArgumentException('不支持的第三方能力分组：' . Tools::limit($group, 40));
        }
    }
}
