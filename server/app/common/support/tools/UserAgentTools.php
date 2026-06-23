<?php

namespace app\common\support\tools;

/**
 * User-Agent 解析工具。
 *
 * 纯本地正则解析浏览器、操作系统、设备类型，不依赖外部服务。
 * Author: qiufeng
 */
trait UserAgentTools
{
    /**
     * 解析 User-Agent 字符串。
     *
     * @param string|null $userAgent UA 字符串;传 null 自动取当前请求的 HTTP_USER_AGENT
     * @return array{browser:string,browser_version:string,os:string,os_version:string,device_type:string,is_robot:bool,user_agent:string} device_type 取 mobile/tablet/desktop/robot/unknown
     */
    public static function parseUserAgent(?string $userAgent = null): array
    {
        $ua = $userAgent ?? ($_SERVER['HTTP_USER_AGENT'] ?? '');

        $result = [
            'browser' => 'Unknown',
            'browser_version' => '',
            'os' => 'Unknown',
            'os_version' => '',
            'device_type' => 'unknown',
            'is_robot' => false,
            'user_agent' => $ua,
        ];

        if ($ua === '') {
            return $result;
        }

        if (preg_match('/(bot|spider|crawl|slurp|curl|wget|python-requests|httpclient)/i', $ua, $m)) {
            $result['is_robot'] = true;
            $result['device_type'] = 'robot';
            $result['browser'] = self::robotName($ua) ?? ucfirst(strtolower($m[1]));
            return $result;
        }

        [$result['os'], $result['os_version']] = self::detectOs($ua);
        [$result['browser'], $result['browser_version']] = self::detectBrowser($ua);
        $result['device_type'] = self::detectDeviceType($ua);

        return $result;
    }

    private static function robotName(string $ua): ?string
    {
        foreach ([
            'Baiduspider' => '百度蜘蛛',
            'Googlebot' => 'Googlebot',
            'bingbot' => 'Bingbot',
            'Sogou' => '搜狗蜘蛛',
            'Bytespider' => '头条蜘蛛',
            '360Spider' => '360蜘蛛',
            'YandexBot' => 'YandexBot',
        ] as $needle => $name) {
            if (stripos($ua, $needle) !== false) {
                return $name;
            }
        }

        return null;
    }

    private static function detectOs(string $ua): array
    {
        if (preg_match('/Windows NT ([\d.]+)/i', $ua, $m)) {
            $version = match ($m[1]) {
                '10.0' => '10/11',
                '6.3' => '8.1',
                '6.2' => '8',
                '6.1' => '7',
                '6.0' => 'Vista',
                '5.1' => 'XP',
                default => $m[1],
            };
            return ['Windows', $version];
        }

        if (preg_match('/iPhone OS ([\d_]+)|iPad.*OS ([\d_]+)/i', $ua, $m)) {
            return ['iOS', str_replace('_', '.', $m[2] ?? $m[1])];
        }

        if (stripos($ua, 'HarmonyOS') !== false || stripos($ua, 'OpenHarmony') !== false) {
            preg_match('/(?:HarmonyOS|OpenHarmony)[\s\/]?([\d.]*)/i', $ua, $m);
            return ['HarmonyOS', $m[1] ?? ''];
        }

        if (preg_match('/Android ([\d.]+)/i', $ua, $m)) {
            return ['Android', $m[1]];
        }

        if (preg_match('/Mac OS X ([\d_.]+)/i', $ua, $m)) {
            return ['macOS', str_replace('_', '.', $m[1])];
        }

        if (stripos($ua, 'Linux') !== false) {
            return ['Linux', ''];
        }

        return ['Unknown', ''];
    }

    private static function detectBrowser(string $ua): array
    {
        $rules = [
            ['MicroMessenger', '微信内置浏览器', '/MicroMessenger\/([\d.]+)/i'],
            ['DingTalk', '钉钉内置浏览器', '/DingTalk\/([\d.]+)/i'],
            ['QQBrowser', 'QQ浏览器', '/QQBrowser\/([\d.]+)/i'],
            ['UCBrowser', 'UC浏览器', '/UCBrowser\/([\d.]+)/i'],
            ['MiuiBrowser', '小米浏览器', '/MiuiBrowser\/([\d.]+)/i'],
            ['HuaweiBrowser', '华为浏览器', '/HuaweiBrowser\/([\d.]+)/i'],
            ['Edg', 'Edge', '/Edg(?:e|A|iOS)?\/([\d.]+)/i'],
            ['OPR', 'Opera', '/OPR\/([\d.]+)/i'],
            ['SamsungBrowser', '三星浏览器', '/SamsungBrowser\/([\d.]+)/i'],
            ['Firefox', 'Firefox', '/Firefox\/([\d.]+)/i'],
            ['CriOS', 'Chrome', '/CriOS\/([\d.]+)/i'],
            ['Chrome', 'Chrome', '/Chrome\/([\d.]+)/i'],
            ['MSIE', 'IE', '/MSIE ([\d.]+)/i'],
            ['Trident', 'IE', '/rv:([\d.]+)/i'],
        ];

        foreach ($rules as [$needle, $name, $pattern]) {
            if (stripos($ua, $needle) !== false) {
                preg_match($pattern, $ua, $m);
                return [$name, $m[1] ?? ''];
            }
        }

        // Safari 必须放最后：几乎所有 WebKit UA 都带 Safari 字样
        if (stripos($ua, 'Safari') !== false) {
            preg_match('/Version\/([\d.]+)/i', $ua, $m);
            return ['Safari', $m[1] ?? ''];
        }

        return ['Unknown', ''];
    }

    private static function detectDeviceType(string $ua): string
    {
        if (preg_match('/iPad|Tablet|PlayBook/i', $ua) || (stripos($ua, 'Android') !== false && stripos($ua, 'Mobile') === false)) {
            return 'tablet';
        }

        if (preg_match('/Mobile|iPhone|Android|HarmonyOS|Windows Phone/i', $ua)) {
            return 'mobile';
        }

        if (preg_match('/Windows NT|Macintosh|X11|Linux/i', $ua)) {
            return 'desktop';
        }

        return 'unknown';
    }
}
