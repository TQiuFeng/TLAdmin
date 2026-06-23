<?php

namespace app\common\service\system;

use app\common\exception\BizException;
use app\common\support\Tools;
use think\facade\Db;

/**
 * 部门管理服务:树形组织结构维护。
 * Author: qiufeng
 */
final class DeptService
{
    public function tree(): array
    {
        $depts = Db::table('tl_admin_dept')
            ->whereNull('delete_time')
            ->order('sort')
            ->order('id')
            ->select()
            ->toArray();

        return Tools::tree($depts);
    }

    public function create(array $data): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw BizException::paramError('部门名称不能为空');
        }

        $parentId = (int) ($data['parent_id'] ?? 0);
        if ($parentId > 0 && !$this->find($parentId)) {
            throw BizException::paramError('上级部门不存在');
        }

        $now = time();

        return (int) Db::table('tl_admin_dept')->insertGetId([
            'parent_id' => $parentId,
            'name' => $name,
            'leader' => (string) ($data['leader'] ?? ''),
            'phone' => (string) ($data['phone'] ?? ''),
            'email' => (string) ($data['email'] ?? ''),
            'sort' => (int) ($data['sort'] ?? 0),
            'status' => (int) ($data['status'] ?? 1),
            'create_time' => $now,
            'update_time' => $now,
        ]);
    }

    public function update(int $id, array $data): void
    {
        if (!$this->find($id)) {
            throw BizException::notFound('部门不存在');
        }

        if (isset($data['parent_id'])) {
            $parentId = (int) $data['parent_id'];
            if ($parentId === $id) {
                throw BizException::paramError('上级部门不能选择自己');
            }
            if (in_array($id, $this->ancestorIds($parentId), true)) {
                throw BizException::paramError('上级部门不能选择自己的下级');
            }
        }

        $updates = [];
        foreach (['name', 'leader', 'phone', 'email'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (string) $data[$field];
            }
        }
        foreach (['parent_id', 'sort', 'status'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (int) $data[$field];
            }
        }

        if ($updates !== []) {
            $updates['update_time'] = time();
            Db::table('tl_admin_dept')->where('id', $id)->update($updates);
        }
    }

    public function delete(int $id): void
    {
        if (!$this->find($id)) {
            throw BizException::notFound('部门不存在');
        }

        $childCount = Db::table('tl_admin_dept')->where('parent_id', $id)->whereNull('delete_time')->count();
        if ($childCount > 0) {
            throw BizException::conflict('存在下级部门,请先删除下级部门');
        }

        $userCount = Db::table('tl_admin_user')->where('dept_id', $id)->whereNull('delete_time')->count();
        if ($userCount > 0) {
            throw BizException::conflict("部门下还有 {$userCount} 个管理员,请先调整归属");
        }

        Db::table('tl_admin_dept')->where('id', $id)->update([
            'delete_time' => time(),
            'update_time' => time(),
        ]);
    }

    /** 部门及其全部下级 ID,数据权限 dept_tree 场景使用 */
    public function selfAndChildrenIds(int $deptId): array
    {
        $all = Db::table('tl_admin_dept')
            ->whereNull('delete_time')
            ->field('id, parent_id')
            ->select()
            ->toArray();

        $children = [];
        foreach ($all as $dept) {
            $children[(int) $dept['parent_id']][] = (int) $dept['id'];
        }

        $result = [$deptId];
        $queue = [$deptId];
        while ($queue !== []) {
            $current = array_shift($queue);
            foreach ($children[$current] ?? [] as $childId) {
                $result[] = $childId;
                $queue[] = $childId;
            }
        }

        return $result;
    }

    private function ancestorIds(int $deptId): array
    {
        $ancestors = [];
        $current = $deptId;

        while ($current > 0) {
            $dept = $this->find($current);
            if (!$dept) {
                break;
            }
            $current = (int) $dept['parent_id'];
            if ($current > 0) {
                $ancestors[] = $current;
            }
        }

        return $ancestors;
    }

    private function find(int $id): ?array
    {
        $dept = Db::table('tl_admin_dept')->where('id', $id)->whereNull('delete_time')->find();

        return $dept ?: null;
    }
}
