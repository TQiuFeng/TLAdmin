<?php

namespace app\adminapi\controller;

use app\adminapi\vo\CreatedVo;
use app\adminapi\vo\PageVo;
use app\adminapi\vo\DemoOrderVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\DeleteMapping;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\PutMapping;
use app\common\router\RestController;
use app\common\service\gen\DemoOrderService;

/**
 * 演示订单控制器。
 *
 * 只负责读取请求参数、调用 DemoOrderService、返回 VO;不写业务逻辑。
 * 路由、权限标识、OpenAPI 文档均由方法上的注解自动生成。
 * Author: qiufeng(代码生成器)
 */
#[RestController('/adminapi/demo-order', tag: '演示订单')]
final class DemoOrderController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly DemoOrderService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    /** 分页列表:查询参数 page/page_size + 各搜索字段 */

    #[GetMapping(permission: 'demo-order:list', summary: '演示订单列表', listOf: DemoOrderVo::class)]
    public function index(): PageVo
    {
        return PageVo::of($this->service->paginate(
            [
                'order_no' => (string) $this->query('order_no', ''),
                'customer_name' => (string) $this->query('customer_name', ''),
                'is_paid' => (string) $this->query('is_paid', ''),
            ],
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20)))
        ), DemoOrderVo::class);
    }

    /** 详情:路径参数 {id} */
    #[GetMapping('/{id}', permission: 'demo-order:list', summary: '演示订单详情')]
    public function show(): DemoOrderVo
    {
        return DemoOrderVo::from($this->service->detail((int) $this->param('id')));
    }

    /** 新增:请求体为表单字段,返回新记录 ID */
    #[PostMapping(permission: 'demo-order:create', summary: '新增演示订单', message: '创建成功', body: [
        'order_no' => ['type' => 'string', 'description' => '订单号'],
        'customer_name' => ['type' => 'string', 'description' => '客户'],
        'goods_name' => ['type' => 'string', 'description' => '商品'],
        'quantity' => ['type' => 'integer', 'description' => '数量'],
        'amount' => ['type' => 'integer', 'description' => '订单金额'],
        'is_paid' => ['type' => 'integer', 'description' => '已支付'],
        'pay_time' => ['type' => 'integer', 'description' => '支付时间'],
        'remark' => ['type' => 'string', 'description' => '备注'],
    ])]
    public function store(): CreatedVo
    {
        return CreatedVo::from(['id' => $this->service->create($this->request->all())]);
    }

    /** 编辑:路径参数 {id} + 请求体表单字段 */
    #[PutMapping('/{id}', permission: 'demo-order:update', summary: '编辑演示订单', message: '更新成功', body: [
        'order_no' => ['type' => 'string', 'description' => '订单号'],
        'customer_name' => ['type' => 'string', 'description' => '客户'],
        'goods_name' => ['type' => 'string', 'description' => '商品'],
        'quantity' => ['type' => 'integer', 'description' => '数量'],
        'amount' => ['type' => 'integer', 'description' => '订单金额'],
        'is_paid' => ['type' => 'integer', 'description' => '已支付'],
        'pay_time' => ['type' => 'integer', 'description' => '支付时间'],
        'remark' => ['type' => 'string', 'description' => '备注'],
    ])]
    public function update(): array
    {
        $this->service->update((int) $this->param('id'), $this->request->all());

        return [];
    }

    /** 删除:路径参数 {id} */
    #[DeleteMapping('/{id}', permission: 'demo-order:delete', summary: '删除演示订单', message: '删除成功')]
    public function destroy(): array
    {
        $this->service->delete((int) $this->param('id'));

        return [];
    }
}
