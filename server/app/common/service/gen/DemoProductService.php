<?php

namespace app\common\service\gen;

use app\common\exception\BizException;
use think\facade\Db;

/**
 * 演示商品业务服务。
 *
 * 负责演示商品的分页查询、详情、新增、编辑、删除。
 * 数据访问统一使用 think-orm 查询构造器 Db::table():它把链式调用直接编译为
 * 预处理 SQL 执行,性能与 ORM Model 等价但更轻量,是 TLAdmin 各 Service 的统一写法。
 * 性能要点:列表只查需要展示的字段并按主键倒序分页;搜索命中的字段建议加索引。
 * Author: qiufeng(代码生成器)
 */
final class DemoProductService
{
    /**
     * 分页查询演示商品列表。
     *
     * @param array $filters  过滤条件,键为字段名,空字符串忽略
     * @param int   $page      页码,从 1 开始
     * @param int   $pageSize  每页条数
     * @return array{list: array<int, array>, pagination: array{page: int, page_size: int, total: int}}
     */
    public function paginate(array $filters, int $page, int $pageSize): array
    {
        $query = Db::table('tl_demo_product')->whereNull('delete_time');

        if (($filters['name'] ?? '') !== '') {
            $query->whereLike('name', '%' . $filters['name'] . '%');
        }
        if (($filters['status'] ?? '') !== '') {
            $query->where('status', (int) $filters['status']);
        }

        // 统计总数:克隆查询,避免被后面的 field/分页影响
        $total = (clone $query)->count();

        // 列表仅查需要展示的字段(非 SELECT *),减少 IO 与内存占用
        $list = $query
            ->field('id, name, price, stock, status, remark, create_time, update_time')
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
     * 按主键查询单条演示商品完整记录(用于详情展示和编辑回填)。
     *
     * @throws BizException 记录不存在时抛出 404
     */
    public function detail(int $id): array
    {
        $row = Db::table('tl_demo_product')->where('id', $id)->whereNull('delete_time')->find();
        if (!$row) {
            throw BizException::notFound('演示商品不存在');
        }

        return $row;
    }

    /**
     * 新增演示商品。
     *
     * 只写入下方显式列出的字段,避免把请求里的多余字段直接落库(字段白名单)。
     *
     * @param array $data 表单提交的数据
     * @return int 新记录的主键 ID
     */
    public function create(array $data): int
    {
        $now = time();

        return (int) Db::table('tl_demo_product')->insertGetId([
            'name' => (string) ($data['name'] ?? ''),
            'price' => (int) ($data['price'] ?? 0),
            'stock' => (int) ($data['stock'] ?? 0),
            'status' => (int) ($data['status'] ?? 0),
            'remark' => (string) ($data['remark'] ?? ''),
            'create_time' => $now,
            'update_time' => $now,
        ]);
    }

    /**
     * 编辑演示商品。只更新本次提交且在白名单内的字段。
     *
     * @throws BizException 记录不存在时抛出 404
     */
    public function update(int $id, array $data): void
    {
        // 先确认记录存在,不存在直接抛 404
        $this->detail($id);

        $updates = [];
        if (array_key_exists('name', $data)) {
            $updates['name'] = (string) $data['name'];
        }
        if (array_key_exists('price', $data)) {
            $updates['price'] = (int) $data['price'];
        }
        if (array_key_exists('stock', $data)) {
            $updates['stock'] = (int) $data['stock'];
        }
        if (array_key_exists('status', $data)) {
            $updates['status'] = (int) $data['status'];
        }
        if (array_key_exists('remark', $data)) {
            $updates['remark'] = (string) $data['remark'];
        }

        // 没有任何可更新字段时跳过写库
        if ($updates !== []) {
            $updates['update_time'] = time();
            Db::table('tl_demo_product')->where('id', $id)->update($updates);
        }
    }

    /**
     * 删除演示商品。
     *
     * @throws BizException 记录不存在时抛出 404
     */
    public function delete(int $id): void
    {
        // 先确认记录存在
        $this->detail($id);
        // 软删除:置 delete_time,数据保留可恢复
        Db::table('tl_demo_product')->where('id', $id)->update(['delete_time' => time()]);
    }
}
