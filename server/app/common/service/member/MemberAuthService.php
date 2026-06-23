<?php

namespace app\common\service\member;

use app\common\cache\RedisClient;
use app\common\exception\BizException;
use app\common\http\Request;
use app\common\service\integration\WechatMiniAppService;
use think\facade\Db;

/**
 * 会员(C 端用户)认证与自助服务。
 *
 * 面向小程序/App/H5 等 C 端,提供注册、登录发 token、按 token 取用户、
 * 获取/修改资料、改手机号、改密码等便捷函数。token 独立存 Redis(member: 前缀),
 * 与后台管理员 token 完全隔离。
 * Author: qiufeng
 */
final class MemberAuthService
{
    private const TABLE = 'tl_user';
    private const TOKEN_PREFIX = 'member:access:';
    private const TOKEN_TTL = 604800; // 7 天

    public function __construct(private readonly WechatMiniAppService $miniApp)
    {
    }

    /**
     * 注册会员并直接登录(返回 token)。
     *
     * @param array  $data username/mobile(至少一个)、password、nickname(可选)
     * @param string $ip   注册来源 IP
     * @return array{token: string, expires_in: int, user_id: int}
     */
    public function register(array $data, string $ip = ''): array
    {
        $username = trim((string) ($data['username'] ?? ''));
        $mobile = trim((string) ($data['mobile'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $nickname = trim((string) ($data['nickname'] ?? ''));

        if ($username === '' && $mobile === '') {
            throw BizException::paramError('账号和手机号至少填写一个');
        }
        if (mb_strlen($password) < 6) {
            throw BizException::paramError('密码至少 6 位');
        }
        if ($username !== '' && $this->existsBy('username', $username)) {
            throw BizException::conflict('账号已被注册');
        }
        if ($mobile !== '' && $this->existsBy('mobile', $mobile)) {
            throw BizException::conflict('手机号已被注册');
        }

        $now = time();
        $userId = (int) Db::table(self::TABLE)->insertGetId([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'nickname' => $nickname !== '' ? $nickname : ($username !== '' ? $username : $mobile),
            'mobile' => $mobile,
            'status' => 1,
            'register_ip' => $ip,
            'create_time' => $now,
            'update_time' => $now,
            'last_login_time' => $now,
        ]);

        return $this->issueToken($userId);
    }

    /**
     * 账号(username 或 mobile)+ 密码登录。
     *
     * @return array{token: string, expires_in: int, user_id: int}
     */
    public function login(string $account, string $password): array
    {
        $account = trim($account);
        $user = Db::table(self::TABLE)
            ->where(static function ($q) use ($account): void {
                $q->where('username', $account)->whereOr('mobile', $account);
            })
            ->whereNull('delete_time')
            ->find();

        if (!$user || (string) $user['password'] === '' || !password_verify($password, (string) $user['password'])) {
            throw BizException::paramError('账号或密码错误');
        }
        if ((int) $user['status'] !== 1) {
            throw BizException::forbidden('账号已被禁用');
        }

        Db::table(self::TABLE)->where('id', $user['id'])->update(['last_login_time' => time()]);

        return $this->issueToken((int) $user['id']);
    }

    /**
     * 微信小程序登录:用 wx.login 的 code 换 openid,首次登录自动建会员,返回 token。
     *
     * @param string $code 小程序端 wx.login 返回的临时登录凭证
     * @param string $ip   登录来源 IP
     * @return array{token: string, expires_in: int, user_id: int, is_new: bool}
     */
    public function loginByMiniApp(string $code, string $ip = ''): array
    {
        $code = trim($code);
        if ($code === '') {
            throw BizException::paramError('缺少登录凭证 code');
        }

        try {
            $session = $this->miniApp->codeToSession($code);
        } catch (\Throwable $e) {
            throw BizException::paramError('小程序登录失败:' . $e->getMessage());
        }

        $openid = trim((string) ($session['openid'] ?? ''));
        if ($openid === '') {
            throw BizException::paramError('未能获取 openid,请检查小程序 AppID/AppSecret 配置');
        }
        $unionid = trim((string) ($session['unionid'] ?? ''));

        $user = Db::table(self::TABLE)->where('wx_openid', $openid)->whereNull('delete_time')->find();
        $now = time();

        if (!$user) {
            $userId = (int) Db::table(self::TABLE)->insertGetId([
                'nickname' => '微信用户',
                'wx_openid' => $openid,
                'wx_unionid' => $unionid,
                'status' => 1,
                'register_ip' => $ip,
                'create_time' => $now,
                'update_time' => $now,
                'last_login_time' => $now,
            ]);

            return $this->issueToken($userId) + ['is_new' => true];
        }

        if ((int) $user['status'] !== 1) {
            throw BizException::forbidden('账号已被禁用');
        }

        $updates = ['last_login_time' => $now];
        // 首次拿到 unionid 时回填(同一开放平台账号下多端共用)
        if ($unionid !== '' && (string) $user['wx_unionid'] === '') {
            $updates['wx_unionid'] = $unionid;
        }
        Db::table(self::TABLE)->where('id', $user['id'])->update($updates);

        return $this->issueToken((int) $user['id']) + ['is_new' => false];
    }

    /**
     * 微信小程序手机号一键授权:用 getPhoneNumber 的 code 解密手机号并绑定到会员。
     * 复用 updateMobile 的全局唯一性校验,返回绑定后的纯手机号(不含国家码)。
     *
     * @param int    $userId 当前会员 ID
     * @param string $code   小程序端 getPhoneNumber 回调返回的 code
     */
    public function bindMiniAppPhone(int $userId, string $code): string
    {
        $code = trim($code);
        if ($code === '') {
            throw BizException::paramError('缺少手机号凭证 code');
        }

        try {
            $result = $this->miniApp->getPhoneNumber($code);
        } catch (\Throwable $e) {
            throw BizException::paramError('获取手机号失败:' . $e->getMessage());
        }

        $phone = trim((string) ($result['phone_info']['purePhoneNumber'] ?? ''));
        if ($phone === '') {
            throw BizException::paramError('未能解析到手机号');
        }

        $this->updateMobile($userId, $phone);

        return $phone;
    }

    /** 退出登录(失效当前 token) */
    public function logout(string $token): void
    {
        if ($token !== '') {
            RedisClient::delete(self::TOKEN_PREFIX . $token);
        }
    }

    /** 由 token 解析会员 ID,无效返回 null */
    public function userIdByToken(string $token): ?int
    {
        if ($token === '') {
            return null;
        }
        $value = RedisClient::get(self::TOKEN_PREFIX . $token);

        return $value === null ? null : (int) $value;
    }

    /** 从请求 Bearer token 取当前会员 ID,未登录/失效抛 401 */
    public function requireUserId(Request $request): int
    {
        $userId = $this->userIdByToken($request->bearerToken());
        if ($userId === null) {
            throw BizException::unauthorized();
        }

        return $userId;
    }

    /** 获取会员信息(已脱敏,不含密码) */
    public function profile(int $userId): array
    {
        $user = Db::table(self::TABLE)->where('id', $userId)->whereNull('delete_time')->find();
        if (!$user) {
            throw BizException::notFound('用户不存在');
        }
        unset($user['password']);

        return $user;
    }

    /** 修改手机号(校验全局唯一) */
    public function updateMobile(int $userId, string $mobile): void
    {
        $mobile = trim($mobile);
        if ($mobile === '') {
            throw BizException::paramError('手机号不能为空');
        }
        $exists = Db::table(self::TABLE)
            ->where('mobile', $mobile)
            ->where('id', '<>', $userId)
            ->whereNull('delete_time')
            ->find();
        if ($exists) {
            throw BizException::conflict('该手机号已被使用');
        }

        $this->profile($userId);
        Db::table(self::TABLE)->where('id', $userId)->update(['mobile' => $mobile, 'update_time' => time()]);
    }

    /** 修改密码(校验原密码;未设过密码时允许直接设置) */
    public function updatePassword(int $userId, string $oldPassword, string $newPassword): void
    {
        $user = $this->profileRaw($userId);
        if ((string) $user['password'] !== '' && !password_verify($oldPassword, (string) $user['password'])) {
            throw BizException::paramError('原密码错误');
        }
        if (mb_strlen($newPassword) < 6) {
            throw BizException::paramError('新密码至少 6 位');
        }

        Db::table(self::TABLE)->where('id', $userId)->update([
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
            'update_time' => time(),
        ]);
    }

    /** 修改基本资料(昵称/头像/邮箱/性别) */
    public function updateProfile(int $userId, array $data): void
    {
        $this->profile($userId);

        $updates = [];
        foreach (['nickname', 'avatar', 'email'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (string) $data[$field];
            }
        }
        if (array_key_exists('gender', $data)) {
            $updates['gender'] = (int) $data['gender'];
        }

        if ($updates !== []) {
            $updates['update_time'] = time();
            Db::table(self::TABLE)->where('id', $userId)->update($updates);
        }
    }

    /** 生成并存储 token */
    private function issueToken(int $userId): array
    {
        $token = bin2hex(random_bytes(32));
        RedisClient::set(self::TOKEN_PREFIX . $token, (string) $userId, self::TOKEN_TTL);

        return ['token' => $token, 'expires_in' => self::TOKEN_TTL, 'user_id' => $userId];
    }

    private function existsBy(string $field, string $value): bool
    {
        return (bool) Db::table(self::TABLE)->where($field, $value)->whereNull('delete_time')->find();
    }

    /** 含密码的原始记录(内部用) */
    private function profileRaw(int $userId): array
    {
        $user = Db::table(self::TABLE)->where('id', $userId)->whereNull('delete_time')->find();
        if (!$user) {
            throw BizException::notFound('用户不存在');
        }

        return $user;
    }
}
