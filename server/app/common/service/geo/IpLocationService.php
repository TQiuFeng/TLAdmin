<?php

namespace app\common\service\geo;

/**
 * IP 归属地本地查询服务。
 *
 * 使用本地 IPv4 区间 CSV，不请求任何外部接口；完整 IP 库可直接替换数据文件。
 * Author: qiufeng
 */
final class IpLocationService
{
    private array $ranges = [];

    public function __construct(private readonly string $dataFile)
    {
    }

    public function lookup(string $ip): array
    {
        $ip = trim($ip);
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return [
                'found' => false,
                'ip' => $ip,
                'message' => '仅支持 IPv4 地址查询',
            ];
        }

        $target = $this->ipToLong($ip);
        $this->load();

        foreach ($this->ranges as $range) {
            if ($target >= $range['start'] && $target <= $range['end']) {
                return [
                    'found' => true,
                    'ip' => $ip,
                    'country' => $range['country'],
                    'province' => $range['province'],
                    'city' => $range['city'],
                    'isp' => $range['isp'],
                    'range' => [
                        'start_ip' => $range['start_ip'],
                        'end_ip' => $range['end_ip'],
                    ],
                ];
            }
        }

        return [
            'found' => false,
            'ip' => $ip,
            'message' => '本地 IP 库未命中',
        ];
    }

    /**
     * 归属地级联(国家 → 省 → 市 → 区县),供屏蔽规则下拉选择。
     *
     * 以标准行政区划(resources/geo/regions.php,全国省市区)为底,
     * 并入本地 IP 库实际出现的地区。说明:IP 库通常只能解析到省/市级,
     * 区县及以下选项即便配置也未必能命中,故乡镇/村级不纳入。
     * @return array<int, array{label: string, value: string, children?: array}>
     */
    public function regions(): array
    {
        // 标准区划底图(嵌套:名称 => 子级 map,叶子为空数组)
        $tree = $this->standardRegionTree();

        // 并入本地 IP 库实际出现的国家/省/市(规范化命名后下拉与匹配一致)
        $this->load();
        foreach ($this->ranges as $range) {
            $country = $range['country'];
            if ($country === '') {
                continue;
            }
            // 内网/保留地址没有省市,不作为屏蔽选项
            if ($range['province'] === '' && in_array($country, ['保留地址', '局域网', '本机地址'], true)) {
                continue;
            }

            $tree[$country] ??= [];
            if ($range['province'] !== '') {
                $tree[$country][$range['province']] ??= [];
                if ($range['city'] !== '') {
                    $tree[$country][$range['province']][$range['city']] ??= [];
                }
            }
        }

        return $this->toCascader($tree);
    }

    /** 嵌套 map(名称 => 子 map)递归转为 cascader 选项 [{label,value,children}] */
    private function toCascader(array $map): array
    {
        $nodes = [];
        foreach ($map as $name => $children) {
            $node = ['label' => (string) $name, 'value' => (string) $name];
            if (is_array($children) && $children !== []) {
                $node['children'] = $this->toCascader($children);
            }
            $nodes[] = $node;
        }

        return $nodes;
    }

    /**
     * 标准行政区划底图(国家 => 省 => 市 => [区县 => []]),完整嵌套,叶子为空数组。
     * 数据来自同目录 regions.php,文件不存在时返回空(只用 IP 库聚合)。
     */
    private function standardRegionTree(): array
    {
        $file = dirname($this->dataFile) . '/regions.php';

        return is_file($file) ? (array) require $file : [];
    }

    private function load(): void
    {
        if ($this->ranges !== []) {
            return;
        }

        if (!is_file($this->dataFile)) {
            throw new \RuntimeException('IP 归属地本地数据文件不存在');
        }

        $handle = fopen($this->dataFile, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('IP 归属地本地数据文件无法读取');
        }

        $headers = fgetcsv($handle) ?: [];
        while (($row = fgetcsv($handle)) !== false) {
            $item = array_combine($headers, $row);
            if (!is_array($item) || empty($item['start_ip']) || empty($item['end_ip'])) {
                continue;
            }

            $this->ranges[] = [
                'start' => $this->ipToLong((string) $item['start_ip']),
                'end' => $this->ipToLong((string) $item['end_ip']),
                'start_ip' => (string) $item['start_ip'],
                'end_ip' => (string) $item['end_ip'],
                'country' => (string) ($item['country'] ?? ''),
                'province' => (string) ($item['province'] ?? ''),
                'city' => (string) ($item['city'] ?? ''),
                'isp' => (string) ($item['isp'] ?? ''),
            ];
        }

        fclose($handle);

        usort($this->ranges, static fn (array $a, array $b): int => $a['start'] <=> $b['start']);
    }

    private function ipToLong(string $ip): int
    {
        $value = ip2long($ip);
        if ($value === false) {
            throw new \InvalidArgumentException('IP 地址格式错误');
        }

        return (int) sprintf('%u', $value);
    }
}
