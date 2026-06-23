<?php

namespace app\common\service\system;

use app\common\exception\BizException;
use think\facade\Db;

/**
 * 角色管理服务:CRUD、分配菜单权限、数据权限。
 * Author: qiufeng
 */
final class RoleService
{
    public function paginate(array $filters, int $page, int $pageSize): array
    {
        $query = Db::table('tl_admin_role')->whereNull('delete_time');

        if (($filters['name'] ?? '') !== '') {
            $query->whereLike('name', '%' . $filters['name'] . '%');
        }
        if (($filters['status'] ?? '') !== '') {
            $query->where('status', (int) $filters['status']);
        }

        $total = (clone $query)->count();
        $list = $query->order('sort')->order('id')->page($page, $pageSize)->select()->toArray();

        return [
            'list' => $list,
            'pagination' => ['page' => $page, 'page_size' => $pageSize, 'total' => $total],
        ];
    }

    public function all(): array
    {
        return Db::table('tl_admin_role')
            ->whereNull('delete_time')
            ->where('status', 1)
            ->field('id, name, code')
            ->order('sort')
            ->select()
            ->toArray();
    }

    public function detail(int $id): array
    {
        $role = $this->mustFind($id);
        $role['menu_ids'] = Db::table('tl_admin_role_menu')->where('role_id', $id)->column('menu_id');
        $role['dept_ids'] = Db::table('tl_admin_role_dept')->where('role_id', $id)->column('dept_id');

        return $role;
    }

    public function create(array $data): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        $code = trim((string) ($data['code'] ?? ''));

        if ($name === '' || $code === '') {
            throw BizException::paramError('角色名称和编码不能为空');
        }
        if (Db::table('tl_admin_role')->where('code', $code)->whereNull('delete_time')->find()) {
            throw BizException::conflict('角色编码已存在');
        }

        $now = time();

        Db::startTrans();
        try {
            $roleId = Db::table('tl_admin_role')->insertGetId([
                'name' => $name,
                'code' => $code,
                'data_scope' => $this->validScope((string) ($data['data_scope'] ?? 'self')),
                'sort' => (int) ($data['sort'] ?? 0),
                'status' => (int) ($data['status'] ?? 1),
                'remark' => (string) ($data['remark'] ?? ''),
                'create_time' => $now,
                'update_time' => $now,
            ]);

            $this->syncRelations((int) $roleId, $data);
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }

        return (int) $roleId;
    }

    public function update(int $id, array $data): void
    {
        $role = $this->mustFind($id);

        if ($role['code'] === 'super_admin' && isset($data['code']) && $data['code'] !== 'super_admin') {
            throw BizException::forbidden('内置超级管理员角色编码不可修改');
        }

        $updates = [];
        foreach (['name', 'remark'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (string) $data[$field];
            }
        }
        if (array_key_exists('code', $data)) {
            $code = trim((string) $data['code']);
            $exists = Db::table('tl_admin_role')->where('code', $code)->whereNull('delete_time')->where('id', '<>', $id)->find();
            if ($exists) {
                throw BizException::conflict('角色编码已存在');
            }
            $updates['code'] = $code;
        }
        if (array_key_exists('data_scope', $data)) {
            $updates['data_scope'] = $this->validScope((string) $data['data_scope']);
        }
        foreach (['sort', 'status'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (int) $data[$field];
            }
        }

        Db::startTrans();
        try {
            if ($updates !== []) {
                $updates['update_time'] = time();
                Db::table('tl_admin_role')->where('id', $id)->update($updates);
            }

            $this->syncRelations($id, $data);
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }
    }

    public function delete(int $id): void
    {
        $role = $this->mustFind($id);

        if ($role['code'] === 'super_admin') {
            throw BizException::forbidden('内置超级管理员角色不可删除');
        }

        $userCount = Db::table('tl_admin_user_role')
            ->alias('ur')
            ->join('tl_admin_user u', 'u.id = ur.user_id')
            ->where('ur.role_id', $id)
            ->whereNull('u.delete_time')
            ->count();
        if ($userCount > 0) {
            throw BizException::conflict("角色下还有 {$userCount} 个管理员,请先移除");
        }

        Db::startTrans();
        try {
            Db::table('tl_admin_role')->where('id', $id)->update([
                'delete_time' => time(),
                'update_time' => time(),
            ]);
            Db::table('tl_admin_role_menu')->where('role_id', $id)->delete();
            Db::table('tl_admin_role_dept')->where('role_id', $id)->delete();
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }
    }

    private function syncRelations(int $roleId, array $data): void
    {
        if (array_key_exists('menu_ids', $data) && is_array($data['menu_ids'])) {
            Db::table('tl_admin_role_menu')->where('role_id', $roleId)->delete();
            foreach (array_unique(array_map('intval', $data['menu_ids'])) as $menuId) {
                Db::table('tl_admin_role_menu')->insert(['role_id' => $roleId, 'menu_id' => $menuId]);
            }
        }

        if (array_key_exists('dept_ids', $data) && is_array($data['dept_ids'])) {
            Db::table('tl_admin_role_dept')->where('role_id', $roleId)->delete();
            foreach (array_unique(array_map('intval', $data['dept_ids'])) as $deptId) {
                Db::table('tl_admin_role_dept')->insert(['role_id' => $roleId, 'dept_id' => $deptId]);
            }
        }
    }

    private function validScope(string $scope): string
    {
        return in_array($scope, ['all', 'self', 'dept', 'dept_tree', 'custom'], true) ? $scope : 'self';
    }

    private function mustFind(int $id): array
    {
        $role = Db::table('tl_admin_role')->where('id', $id)->whereNull('delete_time')->find();
        if (!$role) {
            throw BizException::notFound('角色不存在');
        }

        return $role;
    }
}
