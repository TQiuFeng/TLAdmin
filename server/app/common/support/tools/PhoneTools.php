<?php

namespace app\common\support\tools;

/**
 * 手机号归属地工具。
 *
 * 基于本地全量号段库 support/data/phone/phone.dat（约 50 万号段，二进制索引 + 二分查找），
 * 完全离线，不请求任何外部接口。
 * Author: qiufeng
 */
trait PhoneTools
{
    private static ?string $phoneData = null;

    /**
     * 是否合法的中国大陆手机号(自动剔除非数字字符后校验)。
     *
     * @param string $phone 手机号
     * @return bool 合法返回 true
     */
    public static function isMobile(string $phone): bool
    {
        return preg_match('/^1[3-9]\d{9}$/', preg_replace('/\D+/', '', $phone) ?? '') === 1;
    }

    /**
     * 手机号归属地查询(本地号段库)。
     *
     * @param string $phone 手机号
     * @return array{found:bool,phone:string,prefix?:string,province?:string,city?:string,operator?:string,area_code?:string,postcode?:string,message?:string} 未命中时 found=false 并带 message
     */
    public static function phoneLocation(string $phone): array
    {
        $phone = preg_replace('/\D+/', '', $phone) ?? '';
        if (!self::isMobile($phone)) {
            return ['found' => false, 'phone' => $phone, 'message' => '手机号格式不正确'];
        }

        $data = self::phoneData();
        $indexOffset = unpack('V', substr($data, 4, 4))[1];
        $total = intdiv(strlen($data) - $indexOffset, 9);
        $target = (int) substr($phone, 0, 7);

        $low = 0;
        $high = $total - 1;
        while ($low <= $high) {
            $mid = intdiv($low + $high, 2);
            $entry = unpack('Vprefix/Voffset/Ctype', substr($data, $indexOffset + $mid * 9, 9));
            if ($entry['prefix'] < $target) {
                $low = $mid + 1;
            } elseif ($entry['prefix'] > $target) {
                $high = $mid - 1;
            } else {
                $end = strpos($data, "\x00", $entry['offset']);
                $record = substr($data, $entry['offset'], $end === false ? 32 : $end - $entry['offset']);
                [$province, $city, $postcode, $areaCode] = array_pad(explode('|', $record), 4, '');

                return [
                    'found' => true,
                    'phone' => $phone,
                    'prefix' => substr($phone, 0, 7),
                    'province' => $province,
                    'city' => $city,
                    'operator' => self::phoneIspName($entry['type']),
                    'area_code' => $areaCode,
                    'postcode' => $postcode,
                ];
            }
        }

        return ['found' => false, 'phone' => $phone, 'message' => '本地号段库未命中'];
    }

    private static function phoneIspName(int $type): string
    {
        return match ($type) {
            1 => '中国移动',
            2 => '中国联通',
            3 => '中国电信',
            4 => '中国电信虚拟运营商',
            5 => '中国联通虚拟运营商',
            6 => '中国移动虚拟运营商',
            7 => '中国广电',
            8 => '中国广电虚拟运营商',
            default => '未知运营商',
        };
    }

    private static function phoneData(): string
    {
        if (self::$phoneData === null) {
            $path = __DIR__ . '/../data/phone/phone.dat';
            if (!is_file($path)) {
                throw new \RuntimeException("手机号归属地数据文件不存在：{$path}");
            }
            self::$phoneData = (string) file_get_contents($path);
        }

        return self::$phoneData;
    }
}
