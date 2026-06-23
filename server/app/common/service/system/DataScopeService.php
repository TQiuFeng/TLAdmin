<?php

namespace app\common\service\system;

use think\facade\Db;

/**
 * 数据权限作用域:按用户全部角色的 data_scope 计算可见范围(取并集,就宽不就窄)。
 *
 * data_scope 取值:all 全部 / dept_tree 本部门及以下 / dept 本部门 / custom 自定义部门 / self 仅本人。
 * Author: qiufeng
 */
final class DataScopeService
{
    /**
     * 计算用户可见范围。
     *
     * @return array{all: bool, dept_ids: int[], user_id: int}
     *         all=true 不限制;否则可见 dept_ids 内的数据 + 自己本人的数据
     */
    public function resolve(int $userId): array
    {
        $user = Db::table('tl_admin_user')->where('id', $userId)->whereNull('delete_time')->find();
        if (!$user) {
            return ['all' => false, 'dept_ids' => [], 'user_id' => $userId];
        }
        if ((int) $user['is_super'] === 1) {
            return ['all' => true, 'dept_ids' => [], 'user_id' => $userId];
        }

        $roles = Db::table('tl_admin_user_role')
            ->alias('ur')
            ->join('tl_admin_role r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->where('r.status', 1)
            ->whereNull('r.delete_time')
            ->field('r.id, r.data_scope')
            ->select()
            ->toArray();

        $deptIds = [];
        foreach ($roles as $role) {
            switch ($role['data_scope']) {
                case 'all':
                    return ['all' => true, 'dept_ids' => [], 'user_id' => $userId];
                case 'dept':
                    if ((int) $user['dept_id'] > 0) {
                        $deptIds[] = (int) $user['dept_id'];
                    }
                    break;
                case 'dept_tree':
                    if ((int) $user['dept_id'] > 0) {
                        $deptIds = array_merge($deptIds, $this->deptWithDescendants((int) $user['dept_id']));
                    }
                    break;
                case 'custom':
                    $custom = Db::table('tl_admin_role_dept')->where('role_id', $role['id'])->column('dept_id');
                    $deptIds = array_merge($deptIds, array_map('intval', $custom));
                    break;
                // self:不增加部门范围,兜底"自己的数据"始终可见
            }
        }

        return ['all' => false, 'dept_ids' => array_values(array_unique($deptIds)), 'user_id' => $userId];
    }

    /** 部门 ID + 全部子孙部门 ID(内存递归,部门量级小) */
    private function deptWithDescendants(int $deptId): array
    {
        $rows = Db::table('tl_admin_dept')->whereNull('delete_time')->field('id, parent_id')->select()->toArray();
        $childrenMap = [];
        foreach ($rows as $row) {
            $childrenMap[(int) $row['parent_id']][] = (int) $row['id'];
        }

        $result = [$deptId];
        $stack = [$deptId];
        while ($stack !== []) {
            $current = array_pop($stack);
            foreach ($childrenMap[$current] ?? [] as $child) {
                $result[] = $child;
                $stack[] = $child;
            }
        }

        return $result;
    }
}
