<?php

namespace app\adminapi\controller;

use app\adminapi\vo\AdminUserDetailVo;
use app\adminapi\vo\AdminUserVo;
use app\adminapi\vo\CreatedVo;
use app\adminapi\vo\PageVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\DeleteMapping;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\PutMapping;
use app\common\router\RestController;
use app\common\service\auth\TotpService;
use app\common\service\system\AdminUserService;

/**
 * 管理员管理控制器。
 * Author: qiufeng
 */
#[RestController('/adminapi/system/users', tag: '管理员')]
final class AdminUserController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly AdminUserService $service,
        private readonly TotpService $totpService
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[GetMapping(permission: 'system:user:list', summary: '管理员列表', listOf: AdminUserVo::class)]
    public function index(): PageVo
    {
        return PageVo::of($this->service->paginate(
            [
                'username' => (string) $this->query('username', ''),
                'nickname' => (string) $this->query('nickname', ''),
                'status' => (string) $this->query('status', ''),
                'dept_id' => (string) $this->query('dept_id', ''),
            ],
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20))),
            $this->authUserId()
        ), AdminUserVo::class);
    }

    #[GetMapping('/{id}', permission: 'system:user:list', summary: '管理员详情')]
    public function show(): AdminUserDetailVo
    {
        return AdminUserDetailVo::from($this->service->detail((int) $this->param('id')));
    }

    #[PostMapping(permission: 'system:user:create', summary: '新增管理员', message: '创建成功')]
    public function store(): CreatedVo
    {
        return CreatedVo::from(['id' => $this->service->create($this->request->all())]);
    }

    #[PutMapping('/{id}', permission: 'system:user:update', summary: '编辑管理员(含分配角色)', message: '更新成功')]
    public function update(): array
    {
        $this->service->update((int) $this->param('id'), $this->request->all(), $this->authUserId());

        return [];
    }

    #[DeleteMapping('/{id}', permission: 'system:user:delete', summary: '删除管理员', message: '删除成功')]
    public function destroy(): array
    {
        $this->service->delete((int) $this->param('id'), $this->authUserId());

        return [];
    }

    /** 强制重置(解绑)指定管理员的动态验证码,验证器丢失场景使用 */
    #[PostMapping('/{id}/reset-totp', permission: 'system:user:update', summary: '强制重置管理员动态验证码(验证器丢失)', message: '已重置该账号的动态验证码绑定')]
    public function resetTotp(): array
    {
        $this->totpService->resetForUser((int) $this->param('id'));

        return [];
    }

    #[PostMapping('/{id}/reset-password', permission: 'system:user:reset-password', summary: '重置管理员密码', message: '密码已重置,该账号已强制下线')]
    public function resetPassword(): array
    {
        $this->service->resetPassword(
            (int) $this->param('id'),
            (string) $this->input('password', '')
        );
        return [];
    }
}
