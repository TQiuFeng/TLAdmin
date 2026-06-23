<?php

namespace app\common\service\auth;

use app\common\cache\RedisClient;
use app\common\support\Tools;

/**
 * Token 服务。
 *
 * access token 短有效期用于 API 请求,refresh token 长有效期用于续期;
 * 全部存 Redis,支持主动退出、按用户踢下线(修改密码后失效)。
 * Author: qiufeng
 */
final class TokenService
{
    public const ACCESS_TTL = 7200;          // 2 小时
    public const REFRESH_TTL = 1209600;      // 14 天

    /** @return array{access_token: string, refresh_token: string, expires_in: int} */
    public function issue(int $userId): array
    {
        $accessToken = Tools::randomToken(32);
        $refreshToken = Tools::randomToken(32);

        RedisClient::set("auth:access:{$accessToken}", (string) $userId, self::ACCESS_TTL);
        RedisClient::set("auth:refresh:{$refreshToken}", (string) $userId, self::REFRESH_TTL);
        RedisClient::addToSet("auth:user:{$userId}", $accessToken, self::REFRESH_TTL);
        RedisClient::addToSet("auth:user-refresh:{$userId}", $refreshToken, self::REFRESH_TTL);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => self::ACCESS_TTL,
        ];
    }

    public function verifyAccess(string $accessToken): ?int
    {
        $userId = RedisClient::get("auth:access:{$accessToken}");

        return $userId === null ? null : (int) $userId;
    }

    /** 刷新成功返回新 token 对,refresh token 一次性使用 */
    public function refresh(string $refreshToken): ?array
    {
        $userId = RedisClient::get("auth:refresh:{$refreshToken}");
        if ($userId === null) {
            return null;
        }

        RedisClient::delete("auth:refresh:{$refreshToken}");
        RedisClient::removeFromSet("auth:user-refresh:{$userId}", $refreshToken);

        return $this->issue((int) $userId);
    }

    public function revokeAccess(string $accessToken): void
    {
        $userId = RedisClient::get("auth:access:{$accessToken}");
        RedisClient::delete("auth:access:{$accessToken}");

        if ($userId !== null) {
            RedisClient::removeFromSet("auth:user:{$userId}", $accessToken);
        }
    }

    /** 踢下线:作废某用户的全部 access + refresh token */
    public function revokeAllForUser(int $userId): void
    {
        foreach (RedisClient::setMembers("auth:user:{$userId}") as $token) {
            RedisClient::delete("auth:access:{$token}");
        }
        foreach (RedisClient::setMembers("auth:user-refresh:{$userId}") as $token) {
            RedisClient::delete("auth:refresh:{$token}");
        }

        RedisClient::delete("auth:user:{$userId}", "auth:user-refresh:{$userId}");
    }
}
