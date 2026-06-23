<?php

namespace app\common\support\tools;

/**
 * 经纬度逆地理编码工具。
 *
 * 离线模式：基于本地省市区中心点数据（support/data/geo/area.php）最近邻匹配，返回省市区级地址。
 * 在线增强：配置高德 key（参数 amap_key 或 .env 的 AMAP_KEY）时优先调高德逆地理接口拿门牌级地址，
 * 失败自动降级离线结果，保证无网络时也可用。
 * Author: qiufeng
 */
trait GeoTools
{
    private static ?array $chinaAreaRows = null;
    private static ?array $chinaAreaIndex = null;

    /**
     * 经纬度转详细地址(优先高德,失败降级离线)。
     *
     * @param float $lng     经度
     * @param float $lat     纬度
     * @param array $options 可选项:amap_key(高德 key)、timeout
     * @return array{address:string,province:string,city:string,district:string,distance_km:?float,source:string,lng:float,lat:float} source 为 amap 或 offline
     */
    public static function locateAddress(float $lng, float $lat, array $options = []): array
    {
        $amapKey = $options['amap_key'] ?? self::mailEnv('AMAP_KEY');
        if ($amapKey !== '') {
            $online = self::locateWithAmap($lng, $lat, $amapKey, $options);
            if ($online !== null) {
                return $online;
            }
        }

        return self::locateOffline($lng, $lat);
    }

    /**
     * 纯离线逆地理:匹配最近的区县中心点。
     *
     * @param float $lng 经度
     * @param float $lat 纬度
     * @return array 地址信息,结构同 locateAddress(),source 恒为 offline
     */
    public static function locateOffline(float $lng, float $lat): array
    {
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach (self::chinaAreaData() as $row) {
            if ($row[5] !== 3 || $row[8] === null || $row[9] === null) {
                continue;
            }
            $distance = self::haversineKm($lng, $lat, (float) $row[8], (float) $row[9]);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $row;
            }
        }

        if ($nearest === null) {
            return ['address' => '', 'province' => '', 'city' => '', 'district' => '', 'distance_km' => null, 'source' => 'offline', 'lng' => $lng, 'lat' => $lat];
        }

        $parts = self::areaNameParts($nearest[4]);

        return [
            'address' => implode('', $parts),
            'province' => $parts[0] ?? '',
            'city' => $parts[1] ?? ($parts[0] ?? ''),
            'district' => $parts[count($parts) - 1] ?? '',
            'distance_km' => round($minDistance, 2),
            'source' => 'offline',
            'lng' => $lng,
            'lat' => $lat,
        ];
    }

    /**
     * 两点间球面距离(公里,Haversine 公式)。
     *
     * @param float $lng1 点 1 经度
     * @param float $lat1 点 1 纬度
     * @param float $lng2 点 2 经度
     * @param float $lat2 点 2 纬度
     * @return float 距离(公里,保留 3 位小数)
     */
    public static function distanceKm(float $lng1, float $lat1, float $lng2, float $lat2): float
    {
        return round(self::haversineKm($lng1, $lat1, $lng2, $lat2), 3);
    }

    private static function locateWithAmap(float $lng, float $lat, string $key, array $options): ?array
    {
        $response = self::httpGet('https://restapi.amap.com/v3/geocode/regeo', [
            'timeout' => $options['timeout'] ?? 5,
            'query' => ['key' => $key, 'location' => $lng . ',' . $lat, 'extensions' => 'base'],
        ]);

        $regeo = $response['json']['regeocode'] ?? null;
        if (!$response['ok'] || ($response['json']['status'] ?? '0') !== '1' || !is_array($regeo)) {
            return null;
        }

        $component = $regeo['addressComponent'] ?? [];
        $city = $component['city'] ?? '';

        return [
            'address' => is_string($regeo['formatted_address'] ?? null) ? $regeo['formatted_address'] : '',
            'province' => is_string($component['province'] ?? null) ? $component['province'] : '',
            'city' => is_string($city) && $city !== '' ? $city : (string) ($component['province'] ?? ''),
            'district' => is_string($component['district'] ?? null) ? $component['district'] : '',
            'distance_km' => null,
            'source' => 'amap',
            'lng' => $lng,
            'lat' => $lat,
        ];
    }

    private static function haversineKm(float $lng1, float $lat1, float $lng2, float $lat2): float
    {
        $radLat1 = deg2rad($lat1);
        $radLat2 = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2 + cos($radLat1) * cos($radLat2) * sin($deltaLng / 2) ** 2;

        return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * 拆分 merger_name（如「中国,北京,北京市,朝阳区」），去掉「中国」和直辖市的重复层级。
     */
    private static function areaNameParts(string $mergerName): array
    {
        $parts = array_values(array_filter(explode(',', $mergerName), static fn (string $p): bool => $p !== '中国' && $p !== ''));

        $cleaned = [];
        foreach ($parts as $part) {
            $previous = $cleaned === [] ? null : $cleaned[count($cleaned) - 1];
            if ($previous !== null && str_starts_with($part, $previous)) {
                array_pop($cleaned); // 「北京」+「北京市」只留「北京市」
            }
            $cleaned[] = $part;
        }

        return $cleaned;
    }

    private static function chinaAreaData(): array
    {
        return self::$chinaAreaRows ??= require __DIR__ . '/../data/geo/area.php';
    }

    private static function chinaAreaIndex(): array
    {
        if (self::$chinaAreaIndex === null) {
            self::$chinaAreaIndex = [];
            foreach (self::chinaAreaData() as $row) {
                self::$chinaAreaIndex[$row[0]] = $row;
            }
        }

        return self::$chinaAreaIndex;
    }
}
