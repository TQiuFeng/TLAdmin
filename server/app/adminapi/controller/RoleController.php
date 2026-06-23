<?php

namespace app\adminapi\controller;

use app\adminapi\vo\CreatedVo;
use app\adminapi\vo\PageVo;
use app\adminapi\vo\RoleDetailVo;
use app\adminapi\vo\RoleOptionVo;
use app\adminapi\vo\RoleVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\DeleteMapping;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\PutMapping;
use app\common\router\RestController;
use app\common\service\system\RoleService;

/**
 * 角色管理控制器。
 * Author: qiufeng
 */
#[RestController('/adminapi/system/roles', tag: '角色')]
final class RoleController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly RoleService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[GetMapping(permission: 'system:role:list', summary: '角色列表', listOf: RoleVo::class)]
    public function index(): PageVo
    {
        return PageVo::of($this->service->paginate(
            [
                'name' => (string) $this->query('name', ''),
                'status' => (string) $this->query('status', ''),
            ],
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20)))
        ), RoleVo::class);
    }

    /** @return RoleOptionVo[] 全部启用角色,下拉框使用 */
    #[GetMapping('/all', summary: '全部启用角色(下拉框)', listOf: RoleOptionVo::class)]
    public function all(): array
    {
        return RoleOptionVo::list($this->service->all());
    }

    #[GetMapping('/{id}', permission: 'system:role:list', summary: '角色详情(含菜单权限)')]
    public function show(): RoleDetailVo
    {
        return RoleDetailVo::from($this->service->detail((int) $this->param('id')));
    }

    #[PostMapping(permission: 'system:role:create', summary: '新增角色', message: '创建成功')]
    public function store(): CreatedVo
    {
        return CreatedVo::from(['id' => $this->service->create($this->request->all())]);
    }

    #[PutMapping('/{id}', permission: 'system:role:update', summary: '编辑角色(含分配菜单权限)', message: '更新成功')]
    public function update(): array
    {
        $this->service->update((int) $this->param('id'), $this->request->all());

        return [];
    }

    #[DeleteMapping('/{id}', permission: 'system:role:delete', summary: '删除角色', message: '删除成功')]
    public function destroy(): array
    {
        $this->service->delete((int) $this->param('id'));

        return [];
    }
}
