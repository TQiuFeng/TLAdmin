<?php

namespace app\common\cache;

use Psr\SimpleCache\CacheInterface;

/**
 * 基于 Redis 的 PSR-16(psr/simple-cache 3.0)缓存实现。
 *
 * 用途:注入给 EasyWeChat 等 SDK 存 access_token,绕开 symfony/cache 5.4 自带的
 * Psr16Cache —— 它与本项目已安装的 psr/simple-cache 3.0 版本不兼容会抛异常。
 * 复用统一的 RedisClient(tladmin: 前缀),值用 serialize 包裹以支持任意类型。
 * Author: qiufeng
 */
final class Psr16Cache implements CacheInterface
{
    private const PREFIX = 'psr16:';
    // PSR-16 允许 ttl 为 null 表示「尽量久」,Redis 必须给正整数,折中用一年
    private const DEFAULT_TTL = 31536000;

    public function get(string $key, mixed $default = null): mixed
    {
        $raw = RedisClient::get(self::PREFIX . $key);
        if ($raw === null) {
            return $default;
        }

        $decoded = unserialize($raw);

        return is_array($decoded) && array_key_exists('v', $decoded) ? $decoded['v'] : $default;
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        RedisClient::set(self::PREFIX . $key, serialize(['v' => $value]), $this->ttlSeconds($ttl));

        return true;
    }

    public function delete(string $key): bool
    {
        RedisClient::delete(self::PREFIX . $key);

        return true;
    }

    public function clear(): bool
    {
        // 不支持整库清空(避免误删其他键),SDK 不依赖此能力
        return false;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $result[(string) $key] = $this->get((string) $key, $default);
        }

        return $result;
    }

    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set((string) $key, $value, $ttl);
        }

        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete((string) $key);
        }

        return true;
    }

    public function has(string $key): bool
    {
        return RedisClient::get(self::PREFIX . $key) !== null;
    }

    private function ttlSeconds(null|int|\DateInterval $ttl): int
    {
        if ($ttl === null) {
            return self::DEFAULT_TTL;
        }

        if ($ttl instanceof \DateInterval) {
            return max(1, (new \DateTimeImmutable())->add($ttl)->getTimestamp() - time());
        }

        return max(1, $ttl);
    }
}
