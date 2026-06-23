<?php

namespace app\adminapi\controller;

use app\adminapi\vo\CreatedVo;
use app\adminapi\vo\DeptVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\DeleteMapping;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\PutMapping;
use app\common\router\RestController;
use app\common\service\system\DeptService;

/**
 * 部门管理控制器。
 * Author: qiufeng
 */
#[RestController('/adminapi/system/depts', tag: '部门')]
final class DeptController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly DeptService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    /** @return DeptVo[] */
    #[GetMapping(permission: 'system:dept:list', summary: '部门树', listOf: DeptVo::class)]
    public function tree(): array
    {
        return DeptVo::list($this->service->tree());
    }

    #[PostMapping(permission: 'system:dept:create', summary: '新增部门', message: '创建成功')]
    public function store(): CreatedVo
    {
        return CreatedVo::from(['id' => $this->service->create($this->request->all())]);
    }

    #[PutMapping('/{id}', permission: 'system:dept:update', summary: '编辑部门', message: '更新成功')]
    public function update(): array
    {
        $this->service->update((int) $this->param('id'), $this->request->all());

        return [];
    }

    #[DeleteMapping('/{id}', permission: 'system:dept:delete', summary: '删除部门', message: '删除成功')]
    public function destroy(): array
    {
        $this->service->delete((int) $this->param('id'));

        return [];
    }
}
