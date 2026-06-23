<?php

namespace app\common\service\system;

use app\common\exception\BizException;
use think\facade\Db;

/**
 * 岗位管理服务。
 * Author: qiufeng
 */
final class PostService
{
    public function paginate(array $filters, int $page, int $pageSize): array
    {
        $query = Db::table('tl_admin_post')->whereNull('delete_time');

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
        return Db::table('tl_admin_post')
            ->whereNull('delete_time')
            ->where('status', 1)
            ->field('id, name, code')
            ->order('sort')
            ->select()
            ->toArray();
    }

    public function create(array $data): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        $code = trim((string) ($data['code'] ?? ''));

        if ($name === '' || $code === '') {
            throw BizException::paramError('岗位名称和编码不能为空');
        }
        if (Db::table('tl_admin_post')->where('code', $code)->whereNull('delete_time')->find()) {
            throw BizException::conflict('岗位编码已存在');
        }

        $now = time();

        return (int) Db::table('tl_admin_post')->insertGetId([
            'name' => $name,
            'code' => $code,
            'sort' => (int) ($data['sort'] ?? 0),
            'status' => (int) ($data['status'] ?? 1),
            'remark' => (string) ($data['remark'] ?? ''),
            'create_time' => $now,
            'update_time' => $now,
        ]);
    }

    public function update(int $id, array $data): void
    {
        $this->mustFind($id);

        $updates = [];
        if (array_key_exists('code', $data)) {
            $code = trim((string) $data['code']);
            if (Db::table('tl_admin_post')->where('code', $code)->whereNull('delete_time')->where('id', '<>', $id)->find()) {
                throw BizException::conflict('岗位编码已存在');
            }
            $updates['code'] = $code;
        }
        foreach (['name', 'remark'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (string) $data[$field];
            }
        }
        foreach (['sort', 'status'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (int) $data[$field];
            }
        }

        if ($updates !== []) {
            $updates['update_time'] = time();
            Db::table('tl_admin_post')->where('id', $id)->update($updates);
        }
    }

    public function delete(int $id): void
    {
        $this->mustFind($id);

        $userCount = Db::table('tl_admin_user_post')
            ->alias('up')
            ->join('tl_admin_user u', 'u.id = up.user_id')
            ->where('up.post_id', $id)
            ->whereNull('u.delete_time')
            ->count();
        if ($userCount > 0) {
            throw BizException::conflict("岗位下还有 {$userCount} 个管理员,请先移除");
        }

        Db::table('tl_admin_post')->where('id', $id)->update([
            'delete_time' => time(),
            'update_time' => time(),
        ]);
    }

    private function mustFind(int $id): array
    {
        $post = Db::table('tl_admin_post')->where('id', $id)->whereNull('delete_time')->find();
        if (!$post) {
            throw BizException::notFound('岗位不存在');
        }

        return $post;
    }
}
