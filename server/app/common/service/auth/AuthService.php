<?php

namespace app\common\service\auth;

use app\common\cache\RedisClient;
use app\common\exception\BizException;
use app\common\http\Request;
use app\common\service\geo\IpLocationService;
use app\common\service\log\LoginLogService;
use app\common\support\Tools;
use think\facade\Db;

/**
 * 认证服务。
 *
 * 登录(含失败次数限制)、退出、刷新 token、获取当前用户档案(菜单树+按钮权限)。
 * Author: qiufeng
 */
final class AuthService
{
    private const MAX_LOGIN_FAILS = 5;
    private const LOCK_SECONDS = 600;

    public function __construct(
        private readonly TokenService $tokenService,
        private readonly IpLocationService $ipLocationService,
        private readonly TotpService $totpService,
        private readonly LoginLogService $loginLogService
    ) {
    }

    /** @return array{access_token: string, refresh_token: string, expires_in: int} */
    public function login(string $username, string $password, Request $request, string $totpCode = ''): array
    {
        $username = trim($username);
        if ($username === '' || $password === '') {
            throw BizException::paramError('账号和密码不能为空');
        }

        $ip = $request->clientIp();
        $failKey = "auth:login-fail:{$username}:{$ip}";

        $fails = (int) (RedisClient::get($failKey) ?? '0');
        if ($fails >= self::MAX_LOGIN_FAILS) {
            $minutes = (int) ceil(max(RedisClient::ttl($failKey), 60) / 60);
            throw BizException::tooManyRequests("登录失败次数过多,请 {$minutes} 分钟后再试");
        }

        $user = Db::table('tl_admin_user')
            ->where('username', $username)
            ->whereNull('delete_time')
            ->find();

        if (!$user || !password_verify($password, $user['password'])) {
            RedisClient::increment($failKey, self::LOCK_SECONDS);
            $this->writeLoginLog(0, $username, $request, false, '账号或密码错误');
            throw BizException::paramError('账号或密码错误');
        }

        if ((int) $user['status'] !== 1) {
            $this->writeLoginLog((int) $user['id'], $username, $request, false, '账号已禁用');
            throw BizException::forbidden('账号已被禁用,请联系管理员');
        }

        // 动态验证码二次校验:全局开启且账号已绑定时必须通过
        if ($this->totpService->globalEnabled() && (int) ($user['totp_enabled'] ?? 0) === 1) {
            if (trim($totpCode) === '') {
                throw new BizException('请输入动态验证码', 40000, 400, ['totp_required' => true]);
            }

            if (!$this->totpService->verifyForUser($user, $totpCode)) {
                RedisClient::increment($failKey, self::LOCK_SECONDS);
                $this->writeLoginLog((int) $user['id'], $username, $request, false, '动态验证码错误');
                throw new BizException('动态验证码错误', 40000, 400, ['totp_required' => true]);
            }
        }

        RedisClient::delete($failKey);

        Db::table('tl_admin_user')->where('id', $user['id'])->update([
            'last_login_time' => time(),
            'last_login_ip' => $ip,
            'update_time' => time(),
        ]);

        $this->writeLoginLog((int) $user['id'], $username, $request, true, '');

        return $this->tokenService->issue((int) $user['id']);
    }

    public function logout(string $accessToken): void
    {
        $this->tokenService->revokeAccess($accessToken);
    }

    /** @return array{access_token: string, refresh_token: string, expires_in: int} */
    public function refresh(string $refreshToken): array
    {
        $tokens = $this->tokenService->refresh($refreshToken);
        if ($tokens === null) {
            throw BizException::unauthorized('refresh token 无效或已过期');
        }

        return $tokens;
    }

    /** 当前用户档案:基础信息 + 菜单树 + 权限标识列表 */
    public function profile(int $userId): array
    {
        $user = Db::table('tl_admin_user')
            ->field('id, username, nickname, avatar, email, mobile, dept_id, is_super, totp_enabled, last_login_time, last_login_ip')
            ->where('id', $userId)
            ->whereNull('delete_time')
            ->find();

        if (!$user) {
            throw BizException::unauthorized('账号不存在或已被删除');
        }

        $menus = $this->menusForUser($userId, (bool) $user['is_super']);

        $permissions = array_values(array_filter(array_unique(
            array_column($menus, 'permission')
        )));

        $visibleMenus = array_values(array_filter(
            $menus,
            static fn (array $menu): bool => in_array($menu['type'], ['catalog', 'menu'], true)
        ));

        $roles = Db::table('tl_admin_role')
            ->alias('r')
            ->join('tl_admin_user_role ur', 'ur.role_id = r.id')
            ->where('ur.user_id', $userId)
            ->whereNull('r.delete_time')
            ->field('r.id, r.name, r.code, r.data_scope')
            ->select()
            ->toArray();

        return [
            'user' => $user,
            'roles' => $roles,
            'permissions' => $user['is_super'] ? array_merge(['*'], $permissions) : $permissions,
            'menus' => Tools::tree($visibleMenus),
        ];
    }

    /** 踢下线:作废用户全部 token(修改密码、禁用账号后调用) */
    public function kickUser(int $userId): void
    {
        $this->tokenService->revokeAllForUser($userId);
    }

    /** 校验权限标识;超管恒通过 */
    public function hasPermission(int $userId, string $permission): bool
    {
        return $this->permissionChecker($userId)($permission);
    }

    /**
     * 一次查出用户的权限,返回可反复调用的判断函数;同一请求要判断多个权限时用它,避免重复查库。
     *
     * @return \Closure(string): bool
     */
    public function permissionChecker(int $userId): \Closure
    {
        $user = Db::table('tl_admin_user')->field('id, is_super')->where('id', $userId)->find();
        if (!$user) {
            return static fn (string $permission): bool => false;
        }
        if ((int) $user['is_super'] === 1) {
            return static fn (string $permission): bool => true;
        }

        $permissions = array_flip(array_filter(array_column($this->menusForUser($userId, false), 'permission')));

        return static fn (string $permission): bool => isset($permissions[$permission]);
    }

    private function menusForUser(int $userId, bool $isSuper): array
    {
        $query = Db::table('tl_admin_menu')->where('status', 1)->order('sort')->order('id');

        if (!$isSuper) {
            $menuIds = Db::table('tl_admin_role_menu')
                ->alias('rm')
                ->join('tl_admin_user_role ur', 'ur.role_id = rm.role_id')
                ->join('tl_admin_role r', 'r.id = rm.role_id')
                ->where('ur.user_id', $userId)
                ->where('r.status', 1)
                ->whereNull('r.delete_time')
                ->column('rm.menu_id');

            if ($menuIds === []) {
                return [];
            }

            $query->where('id', 'in', array_unique($menuIds));
        }

        return $query->select()->toArray();
    }

    private function writeLoginLog(int $userId, string $username, Request $request, bool $success, string $message): void
    {
        $ip = $request->clientIp();
        $location = $this->ipLocationService->lookup($ip);

        $this->loginLogService->record([
            'user_id' => $userId,
            'username' => $username,
            'ip' => $ip,
            'location' => is_array($location)
                ? trim(implode(' ', array_filter([$location['province'] ?? '', $location['city'] ?? ''])))
                : '',
            'user_agent' => mb_substr((string) $request->header('user-agent', ''), 0, 500),
            'status' => $success ? 1 : 0,
            'message' => $message,
            'create_time' => time(),
        ]);
    }
}
