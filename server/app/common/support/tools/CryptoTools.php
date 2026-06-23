<?php

namespace app\common\support\tools;

/**
 * 加密和签名工具。
 *
 * 用于配置密钥加密、签名和验签；敏感配置不能明文写入 runtime 配置。
 * Author: qiufeng
 */
trait CryptoTools
{
    /**
     * 计算字符串的 SHA-256 摘要。
     *
     * @param string $value 原文
     * @return string 64 位十六进制摘要
     */
    public static function sha256(string $value): string
    {
        return hash('sha256', $value);
    }

    /**
     * 对参数数组按键名排序后做 HMAC-SHA256 签名。
     *
     * @param array  $payload 待签名的参数键值对
     * @param string $secret  签名密钥
     * @return string 十六进制签名串
     */
    public static function sign(array $payload, string $secret): string
    {
        ksort($payload);

        return hash_hmac('sha256', http_build_query($payload), $secret);
    }

    /**
     * 校验签名是否匹配,使用时间恒定比较防时序攻击。
     *
     * @param array  $payload   参数键值对
     * @param string $secret    签名密钥
     * @param string $signature 待校验的签名串
     * @return bool 匹配返回 true
     */
    public static function verify(array $payload, string $secret, string $signature): bool
    {
        return hash_equals(self::sign($payload, $secret), $signature);
    }

    /**
     * AES-256-CBC 加密,随机 IV。
     *
     * @param string $plain 明文
     * @param string $key   密钥(任意长度,内部哈希为 32 字节)
     * @return string base64 编码的 iv + 密文
     */
    public static function encrypt(string $plain, string $key): string
    {
        $iv = random_bytes(16);
        $cipher = openssl_encrypt($plain, 'AES-256-CBC', self::normalizeCryptoKey($key), OPENSSL_RAW_DATA, $iv);

        if ($cipher === false) {
            throw new \RuntimeException('加密失败');
        }

        return base64_encode($iv . $cipher);
    }

    /**
     * 解密 encrypt() 生成的密文。
     *
     * @param string $encrypted base64 密文(必须由 encrypt() 生成)
     * @param string $key       加密时使用的同一密钥
     * @return string 解密后的明文
     * @throws \InvalidArgumentException|\RuntimeException 密文格式错误或解密失败
     */
    public static function decrypt(string $encrypted, string $key): string
    {
        $raw = base64_decode($encrypted, true);
        if ($raw === false || strlen($raw) <= 16) {
            throw new \InvalidArgumentException('密文格式错误');
        }

        $iv = substr($raw, 0, 16);
        $cipher = substr($raw, 16);
        $plain = openssl_decrypt($cipher, 'AES-256-CBC', self::normalizeCryptoKey($key), OPENSSL_RAW_DATA, $iv);

        if ($plain === false) {
            throw new \RuntimeException('解密失败');
        }

        return $plain;
    }

    private static function normalizeCryptoKey(string $key): string
    {
        return substr(hash('sha256', $key, true), 0, 32);
    }
}
