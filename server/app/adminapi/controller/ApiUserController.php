<?php

namespace app\adminapi\controller;

use app\adminapi\vo\MemberTokenVo;
use app\adminapi\vo\UserVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\PutMapping;
use app\common\router\RestController;
use app\common\service\member\MemberAuthService;

/**
 * C 端用户接口(供小程序/App/H5 调用,与后台 /adminapi 完全隔离)。
 *
 * 全部 auth:false 跳过后台登录鉴权;需要会员身份的接口在方法内用
 * MemberAuthService::requireUserId 校验会员自己的 token(member: 前缀,独立于后台)。
 * Author: qiufeng
 */
#[RestController('/api/user', tag: 'C端用户')]
final class ApiUserController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly MemberAuthService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[PostMapping('/register', summary: '用户注册', message: '注册成功', auth: false, body: [
        'username' => ['type' => 'string', 'description' => '账号(与手机号至少填一个)'],
        'mobile' => ['type' => 'string', 'description' => '手机号(与账号至少填一个)'],
        'password' => ['type' => 'string', 'description' => '密码(至少 6 位)', 'required' => true],
        'nickname' => ['type' => 'string', 'description' => '昵称(可选)'],
    ])]
    public function register(): MemberTokenVo
    {
        return MemberTokenVo::from($this->service->register($this->request->all(), $this->request->clientIp()));
    }

    #[PostMapping('/login', summary: '用户登录', message: '登录成功', auth: false, body: [
        'account' => ['type' => 'string', 'description' => '账号或手机号', 'required' => true],
        'password' => ['type' => 'string', 'description' => '密码', 'required' => true],
    ])]
    public function login(): MemberTokenVo
    {
        return MemberTokenVo::from($this->service->login(
            (string) $this->input('account', ''),
            (string) $this->input('password', '')
        ));
    }

    #[PostMapping('/login/miniapp', summary: '微信小程序登录', message: '登录成功', auth: false, body: [
        'code' => ['type' => 'string', 'description' => 'wx.login 返回的临时登录凭证 code', 'required' => true],
    ])]
    public function loginMiniApp(): MemberTokenVo
    {
        return MemberTokenVo::from($this->service->loginByMiniApp(
            (string) $this->input('code', ''),
            $this->request->clientIp()
        ));
    }

    #[PostMapping('/logout', summary: '退出登录', message: '已退出', auth: false)]
    public function logout(): array
    {
        $this->service->logout($this->request->bearerToken());

        return [];
    }

    #[GetMapping('/profile', summary: '获取当前用户信息', auth: false)]
    public function profile(): UserVo
    {
        $userId = $this->service->requireUserId($this->request);

        return UserVo::from($this->service->profile($userId));
    }

    #[PutMapping('/profile', summary: '修改个人资料', message: '资料已更新', auth: false, body: [
        'nickname' => ['type' => 'string', 'description' => '昵称'],
        'avatar' => ['type' => 'string', 'description' => '头像 URL'],
        'email' => ['type' => 'string', 'description' => '邮箱'],
        'gender' => ['type' => 'integer', 'description' => '性别:0未知 1男 2女'],
    ])]
    public function updateProfile(): array
    {
        $userId = $this->service->requireUserId($this->request);
        $this->service->updateProfile($userId, $this->request->all());

        return [];
    }

    #[PutMapping('/mobile', summary: '修改手机号', message: '手机号已更新', auth: false, body: [
        'mobile' => ['type' => 'string', 'description' => '新手机号', 'required' => true],
    ])]
    public function updateMobile(): array
    {
        $userId = $this->service->requireUserId($this->request);
        $this->service->updateMobile($userId, (string) $this->input('mobile', ''));

        return [];
    }

    #[PostMapping('/mobile/miniapp', summary: '微信小程序手机号一键授权绑定', message: '手机号已绑定', auth: false, body: [
        'code' => ['type' => 'string', 'description' => 'getPhoneNumber 按钮回调返回的 code', 'required' => true],
    ])]
    public function bindMiniAppPhone(): array
    {
        $userId = $this->service->requireUserId($this->request);
        $mobile = $this->service->bindMiniAppPhone($userId, (string) $this->input('code', ''));

        return ['mobile' => $mobile];
    }

    #[PutMapping('/password', summary: '修改密码', message: '密码已修改', auth: false, body: [
        'old_password' => ['type' => 'string', 'description' => '原密码', 'required' => true],
        'new_password' => ['type' => 'string', 'description' => '新密码(至少 6 位)', 'required' => true],
    ])]
    public function updatePassword(): array
    {
        $userId = $this->service->requireUserId($this->request);
        $this->service->updatePassword(
            $userId,
            (string) $this->input('old_password', ''),
            (string) $this->input('new_password', '')
        );

        return [];
    }
}
