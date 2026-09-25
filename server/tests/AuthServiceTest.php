<?php

/**
 * 登录与权限:成功登录发 token、密码错误、禁用账号、连续失败锁定、权限判断。
 * Author: qiufeng
 */

use app\common\cache\RedisClient;
use app\common\exception\BizException;
use app\common\http\Request;
use app\common\service\auth\AuthService;

/** 登录请求;每个用例用不同 IP,失败计数互不影响 */
$request = static fn (string $ip): Request => new Request('POST', '/adminapi/auth/login', [], [], [], '', ['REMOTE_ADDR' => $ip]);

/** 用例结束清掉失败计数和发出的 token */
$cleanupLogin = static function (TestContext $t, string $username, string $ip, int $userId): void {
    $t->defer(static function () use ($t, $username, $ip, $userId): void {
        RedisClient::delete("auth:login-fail:{$username}:{$ip}");
        $t->make(AuthService::class)->kickUser($userId);
    });
};

return [
    '账号密码正确:发出 access / refresh token' => static function (TestContext $t) use ($request, $cleanupLogin): void {
        $t->needsRedis();
        $admin = $t->createAdmin();
        $cleanupLogin($t, $admin['username'], '10.0.0.1', $admin['id']);

        $tokens = $t->make(AuthService::class)->login($admin['username'], $admin['password'], $request('10.0.0.1'));
        $t->true(strlen($tokens['access_token']) >= 32, 'access_token 长度');
        $t->true($tokens['access_token'] !== $tokens['refresh_token'], '两个 token 不同');
        $t->true($tokens['expires_in'] > 0, '有过期时间');
    },

    '密码错误:统一提示,不暴露账号是否存在' => static function (TestContext $t) use ($request, $cleanupLogin): void {
        $t->needsRedis();
        $admin = $t->createAdmin();
        $cleanupLogin($t, $admin['username'], '10.0.0.2', $admin['id']);
        $cleanupLogin($t, 'no_such_user_x', '10.0.0.2', 0);

        $service = $t->make(AuthService::class);
        $wrong = $t->throws(BizException::class, fn () => $service->login($admin['username'], 'wrong-password', $request('10.0.0.2')));
        $missing = $t->throws(BizException::class, fn () => $service->login('no_such_user_x', 'whatever', $request('10.0.0.2')));
        $t->same('账号或密码错误', $wrong->getMessage());
        $t->same($wrong->getMessage(), $missing->getMessage(), '账号不存在和密码错误提示相同');
    },

    '禁用账号:密码正确也不能登录' => static function (TestContext $t) use ($request, $cleanupLogin): void {
        $t->needsRedis();
        $admin = $t->createAdmin(['status' => 0]);
        $cleanupLogin($t, $admin['username'], '10.0.0.3', $admin['id']);

        $e = $t->throws(BizException::class, fn () => $t->make(AuthService::class)->login($admin['username'], $admin['password'], $request('10.0.0.3')));
        $t->same(403, $e->httpStatus());
    },

    '连续失败 5 次锁定:之后密码正确也被拒' => static function (TestContext $t) use ($request, $cleanupLogin): void {
        $t->needsRedis();
        $admin = $t->createAdmin();
        $cleanupLogin($t, $admin['username'], '10.0.0.4', $admin['id']);

        $service = $t->make(AuthService::class);
        for ($i = 0; $i < 5; $i++) {
            $t->throws(BizException::class, fn () => $service->login($admin['username'], 'wrong', $request('10.0.0.4')));
        }
        $e = $t->throws(BizException::class, fn () => $service->login($admin['username'], $admin['password'], $request('10.0.0.4')));
        $t->same(429, $e->httpStatus());
        $t->contains('登录失败次数过多', $e->getMessage());

        // 换个 IP 不受影响(按 账号 + IP 计数)
        $cleanupLogin($t, $admin['username'], '10.0.0.5', $admin['id']);
        $tokens = $service->login($admin['username'], $admin['password'], $request('10.0.0.5'));
        $t->true($tokens['access_token'] !== '', '其他 IP 可以登录');
    },

    '权限:角色有的标识才通过,超管全部通过' => static function (TestContext $t): void {
        $t->needsDb();
        $admin = $t->createAdmin();
        $t->createRole($admin['id'], 'all', ['system:user:list', 'system:user:create']);

        $service = $t->make(AuthService::class);
        $t->same(true, $service->hasPermission($admin['id'], 'system:user:list'));
        $t->same(true, $service->hasPermission($admin['id'], 'system:user:create'));
        $t->same(false, $service->hasPermission($admin['id'], 'system:user:delete'), '没分配的按钮不通过');
        $t->same(false, $service->hasPermission($admin['id'], 'system:role:list'));

        $super = $t->createAdmin(['is_super' => 1]);
        $t->same(true, $service->hasPermission($super['id'], 'anything:at:all'));
        $t->same(false, $service->hasPermission(99999999, 'system:user:list'), '不存在的用户不通过');
    },

    '权限:停用的角色不生效' => static function (TestContext $t): void {
        $t->needsDb();
        $admin = $t->createAdmin();
        $t->createRole($admin['id'], 'all', ['system:user:list'], [], 0);
        $t->same(false, $t->make(AuthService::class)->hasPermission($admin['id'], 'system:user:list'));
    },
];
