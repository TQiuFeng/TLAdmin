<?php

namespace app\common\service\gen;

use app\common\exception\BizException;
use think\facade\Db;

/**
 * 演示订单业务服务。
 *
 * 负责演示订单的分页查询、详情、新增、编辑、删除。
 * 数据访问统一使用 think-orm 查询构造器 Db::table():它把链式调用直接编译为
 * 预处理 SQL 执行,性能与 ORM Model 等价但更轻量,是 TLAdmin 各 Service 的统一写法。
 * 性能要点:列表只查需要展示的字段并按主键倒序分页;搜索命中的字段建议加索引。
 * Author: qiufeng(代码生成器)
 */
final class DemoOrderService
{
    /**
     * 分页查询演示订单列表。
     *
     * @param array $filters  过滤条件,键为字段名,空字符串忽略
     * @param int   $page      页码,从 1 开始
     * @param int   $pageSize  每页条数
     * @return array{list: array<int, array>, pagination: array{page: int, page_size: int, total: int}}
     */
    public function paginate(array $filters, int $page, int $pageSize): array
    {
        $query = Db::table('tl_demo_order')->whereNull('delete_time');

        if (($filters['order_no'] ?? '') !== '') {
            $query->where('order_no', $filters['order_no']);
        }
        if (($filters['customer_name'] ?? '') !== '') {
            $query->whereLike('customer_name', '%' . $filters['customer_name'] . '%');
        }
        if (($filters['is_paid'] ?? '') !== '') {
            $query->where('is_paid', (int) $filters['is_paid']);
        }

        // 统计总数:克隆查询,避免被后面的 field/分页影响
        $total = (clone $query)->count();

        // 列表仅查需要展示的字段(非 SELECT *),减少 IO 与内存占用
        $list = $query
            ->field('id, order_no, customer_name, goods_name, quantity, amount, is_paid, pay_time, create_time')
            ->order('id', 'desc')
            ->page($page, $pageSize)
            ->select()
            ->toArray();

        return [
            'list' => $list,
            'pagination' => ['page' => $page, 'page_size' => $pageSize, 'total' => $total],
        ];
    }

    /**
     * 按主键查询单条演示订单完整记录(用于详情展示和编辑回填)。
     *
     * @throws BizException 记录不存在时抛出 404
     */
    public function detail(int $id): array
    {
        $row = Db::table('tl_demo_order')->where('id', $id)->whereNull('delete_time')->find();
        if (!$row) {
            throw BizException::notFound('演示订单不存在');
        }

        return $row;
    }

    /**
     * 新增演示订单。
     *
     * 只写入下方显式列出的字段,避免把请求里的多余字段直接落库(字段白名单)。
     *
     * @param array $data 表单提交的数据
     * @return int 新记录的主键 ID
     */
    public function create(array $data): int
    {
        $now = time();

        return (int) Db::table('tl_demo_order')->insertGetId([
            'order_no' => (string) ($data['order_no'] ?? ''),
            'customer_name' => (string) ($data['customer_name'] ?? ''),
            'goods_name' => (string) ($data['goods_name'] ?? ''),
            'quantity' => (int) ($data['quantity'] ?? 0),
            'amount' => (int) ($data['amount'] ?? 0),
            'is_paid' => (int) ($data['is_paid'] ?? 0),
            'pay_time' => (int) ($data['pay_time'] ?? 0),
            'remark' => (string) ($data['remark'] ?? ''),
            'create_time' => $now,
            'update_time' => $now,
        ]);
    }

    /**
     * 编辑演示订单。只更新本次提交且在白名单内的字段。
     *
     * @throws BizException 记录不存在时抛出 404
     */
    public function update(int $id, array $data): void
    {
        // 先确认记录存在,不存在直接抛 404
        $this->detail($id);

        $updates = [];
        if (array_key_exists('order_no', $data)) {
            $updates['order_no'] = (string) $data['order_no'];
        }
        if (array_key_exists('customer_name', $data)) {
            $updates['customer_name'] = (string) $data['customer_name'];
        }
        if (array_key_exists('goods_name', $data)) {
            $updates['goods_name'] = (string) $data['goods_name'];
        }
        if (array_key_exists('quantity', $data)) {
            $updates['quantity'] = (int) $data['quantity'];
        }
        if (array_key_exists('amount', $data)) {
            $updates['amount'] = (int) $data['amount'];
        }
        if (array_key_exists('is_paid', $data)) {
            $updates['is_paid'] = (int) $data['is_paid'];
        }
        if (array_key_exists('pay_time', $data)) {
            $updates['pay_time'] = (int) $data['pay_time'];
        }
        if (array_key_exists('remark', $data)) {
            $updates['remark'] = (string) $data['remark'];
        }

        // 没有任何可更新字段时跳过写库
        if ($updates !== []) {
            $updates['update_time'] = time();
            Db::table('tl_demo_order')->where('id', $id)->update($updates);
        }
    }

    /**
     * 删除演示订单。
     *
     * @throws BizException 记录不存在时抛出 404
     */
    public function delete(int $id): void
    {
        // 先确认记录存在
        $this->detail($id);
        // 软删除:置 delete_time,数据保留可恢复
        Db::table('tl_demo_order')->where('id', $id)->update(['delete_time' => time()]);
    }
}
