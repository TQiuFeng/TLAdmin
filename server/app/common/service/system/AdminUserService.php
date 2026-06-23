<?php

namespace app\common\service\system;

use app\common\exception\BizException;
use app\common\service\auth\AuthService;
use think\facade\Db;

/**
 * 管理员管理服务:列表、新增、编辑、删除、重置密码、分配角色。
 * Author: qiufeng
 */
final class AdminUserService
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly DataScopeService $dataScopeService
    ) {
    }

    public function paginate(array $filters, int $page, int $pageSize, int $operatorId = 0): array
    {
        $query = Db::table('tl_admin_user')
            ->alias('u')
            ->leftJoin('tl_admin_dept d', 'd.id = u.dept_id')
            ->whereNull('u.delete_time')
            ->field('u.id, u.username, u.nickname, u.avatar, u.email, u.mobile, u.dept_id, d.name as dept_name, u.is_super, u.status, u.last_login_time, u.last_login_ip, u.remark, u.create_time');

        // 数据权限:非全部范围时,只能看到可见部门内 + 自己的数据
        if ($operatorId > 0) {
            $scope = $this->dataScopeService->resolve($operatorId);
            if (!$scope['all']) {
                $deptIds = $scope['dept_ids'];
                $query->where(static function ($q) use ($deptIds, $operatorId): void {
                    if ($deptIds !== []) {
                        $q->whereIn('u.dept_id', $deptIds)->whereOr('u.id', $operatorId);
                    } else {
                        $q->where('u.id', $operatorId);
                    }
                });
            }
        }

        if (($filters['username'] ?? '') !== '') {
            $query->whereLike('u.username', '%' . $filters['username'] . '%');
        }
        if (($filters['nickname'] ?? '') !== '') {
            $query->whereLike('u.nickname', '%' . $filters['nickname'] . '%');
        }
        if (($filters['status'] ?? '') !== '') {
            $query->where('u.status', (int) $filters['status']);
        }
        if (($filters['dept_id'] ?? '') !== '') {
            $query->where('u.dept_id', (int) $filters['dept_id']);
        }

        $total = (clone $query)->count();
        $list = $query->order('u.id', 'desc')->page($page, $pageSize)->select()->toArray();

        $userIds = array_column($list, 'id');
        $roleMap = [];
        if ($userIds !== []) {
            $relations = Db::table('tl_admin_user_role')
                ->alias('ur')
                ->join('tl_admin_role r', 'r.id = ur.role_id')
                ->where('ur.user_id', 'in', $userIds)
                ->whereNull('r.delete_time')
                ->field('ur.user_id, r.id, r.name')
                ->select()
                ->toArray();
            foreach ($relations as $relation) {
                $roleMap[$relation['user_id']][] = ['id' => $relation['id'], 'name' => $relation['name']];
            }
        }

        foreach ($list as &$item) {
            $item['roles'] = $roleMap[$item['id']] ?? [];
        }

        return [
            'list' => $list,
            'pagination' => ['page' => $page, 'page_size' => $pageSize, 'total' => $total],
        ];
    }

    public function detail(int $id): array
    {
        $user = Db::table('tl_admin_user')
            ->field('id, username, nickname, avatar, email, mobile, dept_id, is_super, status, remark, create_time')
            ->where('id', $id)
            ->whereNull('delete_time')
            ->find();

        if (!$user) {
            throw BizException::notFound('管理员不存在');
        }

        $user['role_ids'] = Db::table('tl_admin_user_role')->where('user_id', $id)->column('role_id');
        $user['post_ids'] = Db::table('tl_admin_user_post')->where('user_id', $id)->column('post_id');

        return $user;
    }

    public function create(array $data): int
    {
        $username = trim((string) ($data['username'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($username === '' || mb_strlen($username) < 3) {
            throw BizException::paramError('账号长度不能少于 3 位');
        }
        if (mb_strlen($password) < 8) {
            throw BizException::paramError('密码长度不能少于 8 位');
        }
        if (Db::table('tl_admin_user')->where('username', $username)->whereNull('delete_time')->find()) {
            throw BizException::conflict('账号已存在');
        }

        $now = time();

        Db::startTrans();
        try {
            $userId = Db::table('tl_admin_user')->insertGetId([
                'username' => $username,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'nickname' => (string) ($data['nickname'] ?? $username),
                'avatar' => (string) ($data['avatar'] ?? ''),
                'email' => (string) ($data['email'] ?? ''),
                'mobile' => (string) ($data['mobile'] ?? ''),
                'dept_id' => (int) ($data['dept_id'] ?? 0),
                'status' => (int) ($data['status'] ?? 1),
                'remark' => (string) ($data['remark'] ?? ''),
                'create_time' => $now,
                'update_time' => $now,
            ]);

            $this->syncRelations($userId, $data);
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }

        return (int) $userId;
    }

    public function update(int $id, array $data, int $operatorId): void
    {
        $user = $this->mustFind($id);

        if ((int) $user['is_super'] === 1 && $id !== $operatorId) {
            throw BizException::forbidden('不能修改超级管理员');
        }

        $updates = [];
        foreach (['nickname', 'avatar', 'email', 'mobile', 'remark'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (string) $data[$field];
            }
        }
        if (array_key_exists('dept_id', $data)) {
            $updates['dept_id'] = (int) $data['dept_id'];
        }
        if (array_key_exists('status', $data)) {
            if ((int) $user['is_super'] === 1 && (int) $data['status'] !== 1) {
                throw BizException::forbidden('不能禁用超级管理员');
            }
            $updates['status'] = (int) $data['status'];
        }

        Db::startTrans();
        try {
            if ($updates !== []) {
                $updates['update_time'] = time();
                Db::table('tl_admin_user')->where('id', $id)->update($updates);
            }

            $this->syncRelations($id, $data);
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }

        // 禁用后立即下线
        if (($updates['status'] ?? 1) === 0) {
            $this->authService->kickUser($id);
        }
    }

    public function delete(int $id, int $operatorId): void
    {
        $user = $this->mustFind($id);

        if ((int) $user['is_super'] === 1) {
            throw BizException::forbidden('不能删除超级管理员');
        }
        if ($id === $operatorId) {
            throw BizException::forbidden('不能删除当前登录账号');
        }

        Db::table('tl_admin_user')->where('id', $id)->update([
            'delete_time' => time(),
            'update_time' => time(),
        ]);

        $this->authService->kickUser($id);
    }

    public function resetPassword(int $id, string $password): void
    {
        $this->mustFind($id);

        if (mb_strlen($password) < 8) {
            throw BizException::paramError('密码长度不能少于 8 位');
        }

        Db::table('tl_admin_user')->where('id', $id)->update([
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'update_time' => time(),
        ]);

        $this->authService->kickUser($id);
    }

    private function syncRelations(int $userId, array $data): void
    {
        if (array_key_exists('role_ids', $data) && is_array($data['role_ids'])) {
            Db::table('tl_admin_user_role')->where('user_id', $userId)->delete();
            foreach (array_unique(array_map('intval', $data['role_ids'])) as $roleId) {
                Db::table('tl_admin_user_role')->insert(['user_id' => $userId, 'role_id' => $roleId]);
            }
        }

        if (array_key_exists('post_ids', $data) && is_array($data['post_ids'])) {
            Db::table('tl_admin_user_post')->where('user_id', $userId)->delete();
            foreach (array_unique(array_map('intval', $data['post_ids'])) as $postId) {
                Db::table('tl_admin_user_post')->insert(['user_id' => $userId, 'post_id' => $postId]);
            }
        }
    }

    private function mustFind(int $id): array
    {
        $user = Db::table('tl_admin_user')->where('id', $id)->whereNull('delete_time')->find();
        if (!$user) {
            throw BizException::notFound('管理员不存在');
        }

        return $user;
    }
}
