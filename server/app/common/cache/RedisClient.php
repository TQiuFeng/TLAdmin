<?php

namespace app\common\cache;

use app\common\config\EnvLoader;
use Redis;

/**
 * Redis 客户端单例。
 *
 * 用于 token 存储、登录失败计数、验证码等;统一加 tladmin: 前缀。
 * Author: qiufeng
 */
final class RedisClient
{
    private const PREFIX = 'tladmin:';

    private static ?Redis $redis = null;

    public static function connection(): Redis
    {
        if (self::$redis === null) {
            $redis = new Redis();
            $redis->connect(
                EnvLoader::get('REDIS_HOST', '127.0.0.1'),
                (int) EnvLoader::get('REDIS_PORT', '6379'),
                3.0
            );

            $password = EnvLoader::get('REDIS_PASSWORD', '');
            if ($password !== '') {
                $redis->auth($password);
            }

            self::$redis = $redis;
        }

        return self::$redis;
    }

    public static function get(string $key): ?string
    {
        $value = self::connection()->get(self::PREFIX . $key);

        return $value === false ? null : (string) $value;
    }

    public static function set(string $key, string $value, int $ttlSeconds): void
    {
        self::connection()->setex(self::PREFIX . $key, $ttlSeconds, $value);
    }

    public static function delete(string ...$keys): void
    {
        if ($keys === []) {
            return;
        }

        self::connection()->del(...array_map(static fn (string $key): string => self::PREFIX . $key, $keys));
    }

    public static function increment(string $key, int $ttlSeconds): int
    {
        $redis = self::connection();
        $count = (int) $redis->incr(self::PREFIX . $key);
        if ($count === 1) {
            $redis->expire(self::PREFIX . $key, $ttlSeconds);
        }

        return $count;
    }

    public static function ttl(string $key): int
    {
        return (int) self::connection()->ttl(self::PREFIX . $key);
    }

    public static function addToSet(string $key, string $member, int $ttlSeconds): void
    {
        $redis = self::connection();
        $redis->sAdd(self::PREFIX . $key, $member);
        $redis->expire(self::PREFIX . $key, $ttlSeconds);
    }

    public static function setMembers(string $key): array
    {
        return self::connection()->sMembers(self::PREFIX . $key) ?: [];
    }

    public static function removeFromSet(string $key, string $member): void
    {
        self::connection()->sRem(self::PREFIX . $key, $member);
    }
}
