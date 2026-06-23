<?php

namespace app\adminapi\controller;

use app\adminapi\vo\CreatedVo;
use app\adminapi\vo\PageVo;
use app\adminapi\vo\PostOptionVo;
use app\adminapi\vo\PostVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\DeleteMapping;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\PutMapping;
use app\common\router\RestController;
use app\common\service\system\PostService;

/**
 * 岗位管理控制器。
 * Author: qiufeng
 */
#[RestController('/adminapi/system/posts', tag: '岗位')]
final class PostController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly PostService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[GetMapping(permission: 'system:post:list', summary: '岗位列表', listOf: PostVo::class)]
    public function index(): PageVo
    {
        return PageVo::of($this->service->paginate(
            [
                'name' => (string) $this->query('name', ''),
                'status' => (string) $this->query('status', ''),
            ],
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20)))
        ), PostVo::class);
    }

    /** @return PostOptionVo[] 全部启用岗位,下拉框使用 */
    #[GetMapping('/all', summary: '全部启用岗位(下拉框)', listOf: PostOptionVo::class)]
    public function all(): array
    {
        return PostOptionVo::list($this->service->all());
    }

    #[PostMapping(permission: 'system:post:create', summary: '新增岗位', message: '创建成功')]
    public function store(): CreatedVo
    {
        return CreatedVo::from(['id' => $this->service->create($this->request->all())]);
    }

    #[PutMapping('/{id}', permission: 'system:post:update', summary: '编辑岗位', message: '更新成功')]
    public function update(): array
    {
        $this->service->update((int) $this->param('id'), $this->request->all());

        return [];
    }

    #[DeleteMapping('/{id}', permission: 'system:post:delete', summary: '删除岗位', message: '删除成功')]
    public function destroy(): array
    {
        $this->service->delete((int) $this->param('id'));

        return [];
    }
}
