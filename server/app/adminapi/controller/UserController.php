<?php

namespace app\adminapi\controller;

use app\adminapi\vo\CreatedVo;
use app\adminapi\vo\PageVo;
use app\adminapi\vo\UserVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\DeleteMapping;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\PutMapping;
use app\common\router\RestController;
use app\common\service\member\UserService;

/**
 * 会员用户控制器(C 端会员管理,区别于管理员)。
 * Author: qiufeng
 */
#[RestController('/adminapi/member/users', tag: '会员')]
final class UserController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly UserService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[GetMapping(permission: 'member:user:list', summary: '会员列表', listOf: UserVo::class)]
    public function index(): PageVo
    {
        return PageVo::of($this->service->paginate(
            [
                'keyword' => (string) $this->query('keyword', ''),
                'status' => (string) $this->query('status', ''),
            ],
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20)))
        ), UserVo::class);
    }

    #[GetMapping('/{id}', permission: 'member:user:list', summary: '会员详情')]
    public function show(): UserVo
    {
        return UserVo::from($this->service->detail((int) $this->param('id')));
    }

    #[PostMapping(permission: 'member:user:create', summary: '新增会员', message: '创建成功')]
    public function store(): CreatedVo
    {
        return CreatedVo::from(['id' => $this->service->create($this->request->all())]);
    }

    #[PutMapping('/{id}', permission: 'member:user:update', summary: '编辑会员', message: '更新成功')]
    public function update(): array
    {
        $this->service->update((int) $this->param('id'), $this->request->all());

        return [];
    }

    #[DeleteMapping('/{id}', permission: 'member:user:delete', summary: '删除会员', message: '删除成功')]
    public function destroy(): array
    {
        $this->service->delete((int) $this->param('id'));

        return [];
    }
}
