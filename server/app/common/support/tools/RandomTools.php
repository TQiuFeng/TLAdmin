<?php

namespace app\common\support\tools;

/**
 * 随机数和随机字符串工具。
 *
 * 所有随机值都使用 random_int/random_bytes，适合验证码、token、随机字符串等场景。
 * Author: qiufeng
 */
trait RandomTools
{
    /**
     * 生成区间内的安全随机整数。
     *
     * @param int $min 最小值(含),默认 0
     * @param int $max 最大值(含),默认 PHP_INT_MAX
     * @return int 随机整数
     */
    public static function randomInt(int $min = 0, int $max = PHP_INT_MAX): int
    {
        return random_int($min, $max);
    }

    /**
     * 生成纯数字串(可含前导零),适合短信验证码。
     *
     * @param int $length 位数,默认 6
     * @return string 数字字符串
     */
    public static function randomNumeric(int $length = 6): string
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= (string) random_int(0, 9);
        }

        return $code;
    }

    /**
     * 从给定字母表随机取字符生成定长字符串。
     *
     * @param int    $length   长度,默认 32
     * @param string $alphabet 字符集,默认数字 + 大小写字母
     * @return string 随机字符串
     */
    public static function randomString(int $length = 32, string $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        $max = strlen($alphabet) - 1;
        $value = '';

        for ($i = 0; $i < $length; $i++) {
            $value .= $alphabet[random_int(0, $max)];
        }

        return $value;
    }

    /**
     * 生成随机字节并转十六进制串,适合 token。
     *
     * @param int $bytes 随机字节数,默认 32
     * @return string 十六进制串,长度为 $bytes 的两倍
     */
    public static function randomToken(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes));
    }
}
