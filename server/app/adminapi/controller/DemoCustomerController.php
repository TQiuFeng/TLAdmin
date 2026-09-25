<?php

namespace app\adminapi\controller;

use app\adminapi\vo\CreatedVo;
use app\adminapi\vo\PageVo;
use app\adminapi\vo\DemoCustomerVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\DeleteMapping;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\PutMapping;
use app\common\router\RestController;
use app\common\service\gen\DemoCustomerService;

/**
 * 演示客户控制器。
 *
 * 只负责读取请求参数、调用 DemoCustomerService、返回 VO;不写业务逻辑。
 * 路由、权限标识、OpenAPI 文档均由方法上的注解自动生成。
 * Author: qiufeng(代码生成器)
 */
#[RestController('/adminapi/demo-customer', tag: '演示客户')]
final class DemoCustomerController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly DemoCustomerService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    /** 分页列表:查询参数 page/page_size + 各搜索字段 */

    #[GetMapping(permission: 'demo-customer:list', summary: '演示客户列表', listOf: DemoCustomerVo::class)]
    public function index(): PageVo
    {
        return PageVo::of($this->service->paginate(
            [
                'name' => (string) $this->query('name', ''),
                'contact' => (string) $this->query('contact', ''),
                'mobile' => (string) $this->query('mobile', ''),
                'source' => (string) $this->query('source', ''),
                'next_follow_time_start' => (string) $this->query('next_follow_time_start', ''),
                'next_follow_time_end' => (string) $this->query('next_follow_time_end', ''),
                'status' => (string) $this->query('status', ''),
            ],
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20)))
        ), DemoCustomerVo::class);
    }

    /** 详情:路径参数 {id} */
    #[GetMapping('/{id}', permission: 'demo-customer:list', summary: '演示客户详情')]
    public function show(): DemoCustomerVo
    {
        return DemoCustomerVo::from($this->service->detail((int) $this->param('id')));
    }

    /** 新增:请求体为表单字段,返回新记录 ID */
    #[PostMapping(permission: 'demo-customer:create', summary: '新增演示客户', message: '创建成功', body: [
        'name' => ['type' => 'string', 'description' => '客户名称'],
        'contact' => ['type' => 'string', 'description' => '联系人'],
        'mobile' => ['type' => 'string', 'description' => '手机号'],
        'source' => ['type' => 'string', 'description' => '客户来源'],
        'level' => ['type' => 'integer', 'description' => '客户等级'],
        'next_follow_time' => ['type' => 'integer', 'description' => '下次跟进'],
        'status' => ['type' => 'integer', 'description' => '状态'],
        'remark' => ['type' => 'string', 'description' => '备注'],
    ])]
    public function store(): CreatedVo
    {
        return CreatedVo::from(['id' => $this->service->create($this->request->all())]);
    }

    /** 编辑:路径参数 {id} + 请求体表单字段 */
    #[PutMapping('/{id}', permission: 'demo-customer:update', summary: '编辑演示客户', message: '更新成功', body: [
        'name' => ['type' => 'string', 'description' => '客户名称'],
        'contact' => ['type' => 'string', 'description' => '联系人'],
        'mobile' => ['type' => 'string', 'description' => '手机号'],
        'source' => ['type' => 'string', 'description' => '客户来源'],
        'level' => ['type' => 'integer', 'description' => '客户等级'],
        'next_follow_time' => ['type' => 'integer', 'description' => '下次跟进'],
        'status' => ['type' => 'integer', 'description' => '状态'],
        'remark' => ['type' => 'string', 'description' => '备注'],
    ])]
    public function update(): array
    {
        $this->service->update((int) $this->param('id'), $this->request->all());

        return [];
    }

    /** 删除:路径参数 {id} */
    #[DeleteMapping('/{id}', permission: 'demo-customer:delete', summary: '删除演示客户', message: '删除成功')]
    public function destroy(): array
    {
        $this->service->delete((int) $this->param('id'));

        return [];
    }
}
