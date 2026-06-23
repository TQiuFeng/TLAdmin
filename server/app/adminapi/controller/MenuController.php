<?php

namespace app\adminapi\controller;

use app\adminapi\vo\CreatedVo;
use app\adminapi\vo\MenuVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\DeleteMapping;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\PutMapping;
use app\common\router\RestController;
use app\common\service\system\MenuService;

/**
 * 菜单管理控制器。
 * Author: qiufeng
 */
#[RestController('/adminapi/system/menus', tag: '菜单')]
final class MenuController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly MenuService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    /** @return MenuVo[] */
    #[GetMapping(permission: 'system:menu:list', summary: '菜单树', listOf: MenuVo::class)]
    public function tree(): array
    {
        return MenuVo::list($this->service->tree());
    }

    #[PostMapping(permission: 'system:menu:create', summary: '新增菜单/权限节点', message: '创建成功')]
    public function store(): CreatedVo
    {
        return CreatedVo::from(['id' => $this->service->create($this->request->all())]);
    }

    #[PutMapping('/{id}', permission: 'system:menu:update', summary: '编辑菜单', message: '更新成功')]
    public function update(): array
    {
        $this->service->update((int) $this->param('id'), $this->request->all());

        return [];
    }

    #[DeleteMapping('/{id}', permission: 'system:menu:delete', summary: '删除菜单', message: '删除成功')]
    public function destroy(): array
    {
        $this->service->delete((int) $this->param('id'));

        return [];
    }
}
