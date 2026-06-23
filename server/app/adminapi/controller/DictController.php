<?php

namespace app\adminapi\controller;

use app\adminapi\vo\CreatedVo;
use app\adminapi\vo\DictDataVo;
use app\adminapi\vo\DictOptionVo;
use app\adminapi\vo\DictTypeVo;
use app\adminapi\vo\PageVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\DeleteMapping;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\PutMapping;
use app\common\router\RestController;
use app\common\service\system\DictService;

/**
 * 字典管理控制器:字典类型 + 字典数据。
 * Author: qiufeng
 */
#[RestController('/adminapi/system/dict', tag: '字典')]
final class DictController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly DictService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[GetMapping('/types', permission: 'system:dict:list', summary: '字典类型列表', listOf: DictTypeVo::class)]
    public function types(): PageVo
    {
        return PageVo::of($this->service->typePaginate(
            [
                'name' => (string) $this->query('name', ''),
                'code' => (string) $this->query('code', ''),
            ],
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20)))
        ), DictTypeVo::class);
    }

    #[PostMapping('/types', permission: 'system:dict:create', summary: '新增字典类型', message: '创建成功')]
    public function storeType(): CreatedVo
    {
        return CreatedVo::from(['id' => $this->service->createType($this->request->all())]);
    }

    #[PutMapping('/types/{id}', permission: 'system:dict:update', summary: '编辑字典类型', message: '更新成功')]
    public function updateType(): array
    {
        $this->service->updateType((int) $this->param('id'), $this->request->all());

        return [];
    }

    #[DeleteMapping('/types/{id}', permission: 'system:dict:delete', summary: '删除字典类型', message: '删除成功')]
    public function destroyType(): array
    {
        $this->service->deleteType((int) $this->param('id'));

        return [];
    }

    /** @return DictOptionVo[] 按类型编码查字典项(下拉框接口,登录即可访问) */
    #[GetMapping('/adminapi/dicts/{code}', summary: '按编码取字典项(下拉框)', listOf: DictOptionVo::class)]
    public function dataByCode(): array
    {
        return DictOptionVo::list($this->service->dataByCode((string) $this->param('code')));
    }

    #[GetMapping('/data', permission: 'system:dict:list', summary: '字典数据列表', listOf: DictDataVo::class)]
    public function data(): PageVo
    {
        return PageVo::of($this->service->dataPaginate(
            (string) $this->query('type_code', ''),
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20)))
        ), DictDataVo::class);
    }

    #[PostMapping('/data', permission: 'system:dict:create', summary: '新增字典项', message: '创建成功')]
    public function storeData(): CreatedVo
    {
        return CreatedVo::from(['id' => $this->service->createData($this->request->all())]);
    }

    #[PutMapping('/data/{id}', permission: 'system:dict:update', summary: '编辑字典项', message: '更新成功')]
    public function updateData(): array
    {
        $this->service->updateData((int) $this->param('id'), $this->request->all());

        return [];
    }

    #[DeleteMapping('/data/{id}', permission: 'system:dict:delete', summary: '删除字典项', message: '删除成功')]
    public function destroyData(): array
    {
        $this->service->deleteData((int) $this->param('id'));

        return [];
    }
}
