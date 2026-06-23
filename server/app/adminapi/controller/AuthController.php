<?php

namespace app\adminapi\controller;

use app\adminapi\vo\ProfileVo;
use app\adminapi\vo\TokenVo;
use app\adminapi\vo\TotpSetupVo;
use app\adminapi\vo\TotpStatusVo;
use app\common\config\ConfigRepository;
use app\common\exception\BizException;
use app\common\response\ApiResponseFactory;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\RestController;
use app\common\service\auth\AuthService;
use app\common\service\auth\TotpService;
use think\facade\Db;

/**
 * 认证控制器:登录、退出、刷新 token、当前用户档案、修改密码。
 * Author: qiufeng
 */
#[RestController('/adminapi/auth', tag: '认证')]
final class AuthController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly AuthService $authService,
        private readonly TotpService $totpService
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[PostMapping('/login', summary: '登录', message: '登录成功', auth: false, body: [
        'username' => ['type' => 'string', 'description' => '账号', 'required' => true, 'example' => 'admin'],
        'password' => ['type' => 'string', 'description' => '密码', 'required' => true, 'example' => 'admin123456'],
        'code' => ['type' => 'string', 'description' => '动态验证码(开启 TOTP 且已绑定时必填)'],
    ])]
    public function login(): TokenVo
    {
        return TokenVo::from($this->authService->login(
            (string) $this->input('username', ''),
            (string) $this->input('password', ''),
            $this->request,
            (string) $this->input('code', '')
        ));
    }

    /** 当前用户动态验证码绑定状态 */
    #[GetMapping('/totp/status', summary: '动态验证码绑定状态', tag: '动态验证码')]
    public function totpStatus(): TotpStatusVo
    {
        return TotpStatusVo::from($this->totpService->status($this->authUserId()));
    }

    /** 生成待绑定密钥和 otpauth URI(前端渲染二维码) */
    #[PostMapping('/totp/setup', summary: '生成绑定密钥和二维码 URI', tag: '动态验证码')]
    public function totpSetup(): TotpSetupVo
    {
        return TotpSetupVo::from($this->totpService->setup($this->authUserId()));
    }

    /** 输入动态码确认绑定 */
    #[PostMapping('/totp/confirm', summary: '输入 6 位动态码确认绑定 {code}', message: '动态验证码绑定成功', tag: '动态验证码', body: [
        'code' => ['type' => 'string', 'description' => '验证器显示的 6 位动态码', 'required' => true],
    ])]
    public function totpConfirm(): array
    {
        $this->totpService->confirm($this->authUserId(), (string) $this->input('code', ''));

        return [];
    }

    /** 输入动态码解绑 */
    #[PostMapping('/totp/disable', summary: '输入 6 位动态码解绑 {code}', message: '已解绑动态验证码', tag: '动态验证码', body: [
        'code' => ['type' => 'string', 'description' => '验证器显示的 6 位动态码', 'required' => true],
    ])]
    public function totpDisable(): array
    {
        $this->totpService->disable($this->authUserId(), (string) $this->input('code', ''));

        return [];
    }

    #[PostMapping('/logout', summary: '退出登录', message: '退出成功')]
    public function logout(): array
    {
        $this->authService->logout($this->request->bearerToken());

        return [];
    }

    #[PostMapping('/refresh', summary: '刷新 token', auth: false, body: [
        'refresh_token' => ['type' => 'string', 'description' => '登录时返回的 refresh_token', 'required' => true],
    ])]
    public function refresh(): TokenVo
    {
        $refreshToken = (string) $this->input('refresh_token', '');
        if ($refreshToken === '') {
            throw BizException::paramError('refresh_token 不能为空');
        }

        return TokenVo::from($this->authService->refresh($refreshToken));
    }

    #[GetMapping('/profile', summary: '当前用户档案(含菜单树和权限)')]
    public function profile(): ProfileVo
    {
        return ProfileVo::from($this->authService->profile($this->authUserId()));
    }

    #[PostMapping('/password', summary: '修改自己的密码', message: '密码已修改,请重新登录', body: [
        'old_password' => ['type' => 'string', 'description' => '原密码', 'required' => true],
        'new_password' => ['type' => 'string', 'description' => '新密码(至少 8 位)', 'required' => true],
    ])]
    public function updatePassword(): array
    {
        $oldPassword = (string) $this->input('old_password', '');
        $newPassword = (string) $this->input('new_password', '');

        if (mb_strlen($newPassword) < 8) {
            throw BizException::paramError('新密码长度不能少于 8 位');
        }

        $user = Db::table('tl_admin_user')->where('id', $this->authUserId())->find();
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            throw BizException::paramError('原密码错误');
        }

        Db::table('tl_admin_user')->where('id', $user['id'])->update([
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
            'update_time' => time(),
        ]);

        // 修改密码后全端下线,需重新登录
        $this->authService->kickUser((int) $user['id']);

        return [];
    }
}
