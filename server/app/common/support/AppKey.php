<?php

namespace app\common\support;

use app\common\config\EnvLoader;

/**
 * 应用密钥(APP_KEY):加密第三方配置里的密钥、动态验证码密钥等。
 *
 * - 生产环境必须设置 APP_KEY,否则直接报错,不再悄悄退回公开的默认值;
 * - 本地/测试环境没设置时用 LEGACY_DEV_KEY,方便开箱即用;
 * - 解密时先用当前密钥,失败再试 LEGACY_DEV_KEY 和调用方给的旧密钥,
 *   兼容旧版本(旧版配置层读不到 .env 里的 APP_KEY,第三方密钥实际是用默认值加密的)。
 *   用 php bin/console secrets:reencrypt 可以把存量密文统一换成当前密钥。
 * Author: qiufeng
 */
final class AppKey
{
    /** 旧版本的兜底密钥,源码公开,只能用于本地开发 */
    public const LEGACY_DEV_KEY = 'tladmin-local-dev-key';

    /** 允许不设 APP_KEY 的环境 */
    private const DEV_ENVS = ['local', 'dev', 'development', 'testing', 'test'];

    public static function current(): string
    {
        $key = (string) EnvLoader::get('APP_KEY', '');
        if ($key !== '') {
            return $key;
        }

        $env = strtolower((string) EnvLoader::get('APP_ENV', 'local'));
        if (in_array($env, self::DEV_ENVS, true)) {
            return self::LEGACY_DEV_KEY;
        }

        throw new \RuntimeException('生产环境必须在 .env 中设置 APP_KEY,可执行 php bin/console key:generate 生成');
    }

    public static function encrypt(string $plain): string
    {
        return Tools::encrypt($plain, self::current());
    }

    /**
     * 依次用 当前密钥 → 调用方给的旧密钥 → 旧版默认密钥 解密。
     *
     * @param string[] $previousKeys 轮换密钥时的旧 APP_KEY
     */
    public static function decrypt(string $cipher, array $previousKeys = []): string
    {
        $keys = array_values(array_unique(array_filter([self::current(), ...$previousKeys, self::LEGACY_DEV_KEY])));

        $last = null;
        foreach ($keys as $key) {
            try {
                return self::decryptWith($cipher, $key);
            } catch (\Throwable $e) {
                $last = $e;
            }
        }

        throw new \RuntimeException('解密失败:APP_KEY 与加密时不一致', 0, $last);
    }

    /** 当前密钥能否直接解开(用于判断是否需要重新加密) */
    public static function isCurrent(string $cipher): bool
    {
        try {
            self::decryptWith($cipher, self::current());

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * AES-CBC 没有完整性校验,用错的密钥解密约 1/256 的概率能"成功"并得到乱码;
     * 存的都是文本密钥,不是合法 UTF-8 的结果一律当作解密失败。
     */
    private static function decryptWith(string $cipher, string $key): string
    {
        $plain = Tools::decrypt($cipher, $key);
        if (!mb_check_encoding($plain, 'UTF-8')) {
            throw new \RuntimeException('解密结果不是文本,密钥不对');
        }

        return $plain;
    }

    /**
     * 生成新密钥写入 .env 的 APP_KEY。
     *
     * @param bool $force 已有非空 APP_KEY 时是否覆盖(覆盖会让已加密的数据用新密钥解不开)
     * @return bool 是否写入
     */
    public static function writeToEnvFile(string $envFile, bool $force = false): bool
    {
        $content = (string) file_get_contents($envFile);
        $hasKey = preg_match('/^APP_KEY=(.*)$/m', $content, $matched) === 1;
        if ($hasKey && trim($matched[1]) !== '' && !$force) {
            return false;
        }

        $line = 'APP_KEY=' . self::generate();
        $content = $hasKey
            ? (string) preg_replace('/^APP_KEY=.*$/m', $line, $content, 1)
            : rtrim($content) . "\n{$line}\n";
        file_put_contents($envFile, $content);

        return true;
    }

    /** 生成新的随机密钥 */
    public static function generate(): string
    {
        return 'base64:' . base64_encode(random_bytes(32));
    }
}
