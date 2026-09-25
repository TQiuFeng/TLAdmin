<?php

namespace app\common\service\member;

use app\common\exception\BizException;
use think\facade\Db;

/**
 * 会员用户服务:列表、详情、新增、编辑、删除、调整余额。
 *
 * 面向 C 端会员(tl_user),与后台管理员(tl_admin_user)完全独立。
 * Author: qiufeng
 */
final class UserService
{
    private const TABLE = 'tl_user';

    /** 列表只查展示字段,避免 SELECT * */
    private const LIST_FIELDS = 'id, username, nickname, avatar, mobile, email, gender, status, balance, points, register_ip, remark, last_login_time, create_time';

    /**
     * 分页查询会员列表。
     *
     * @param array $filters 支持 keyword(账号/昵称/手机号模糊)、status(精确)
     */
    public function paginate(array $filters, int $page, int $pageSize): array
    {
        $query = Db::table(self::TABLE)->whereNull('delete_time');

        if (($filters['keyword'] ?? '') !== '') {
            $kw = '%' . $filters['keyword'] . '%';
            $query->where(static function ($q) use ($kw): void {
                $q->whereLike('username', $kw)
                    ->whereOr('nickname', 'like', $kw)
                    ->whereOr('mobile', 'like', $kw);
            });
        }
        if (($filters['status'] ?? '') !== '') {
            $query->where('status', (int) $filters['status']);
        }

        $total = (clone $query)->count();
        $list = $query->field(self::LIST_FIELDS)->order('id', 'desc')->page($page, $pageSize)->select()->toArray();

        return [
            'list' => $list,
            'pagination' => ['page' => $page, 'page_size' => $pageSize, 'total' => $total],
        ];
    }

    public function detail(int $id): array
    {
        $row = Db::table(self::TABLE)->where('id', $id)->whereNull('delete_time')->find();
        if (!$row) {
            throw BizException::notFound('会员不存在');
        }

        return $row;
    }

    /**
     * 新增会员。账号或手机号至少填一个,昵称必填。
     *
     * @return int 新会员 ID
     */
    public function create(array $data): int
    {
        $nickname = trim((string) ($data['nickname'] ?? ''));
        if ($nickname === '') {
            throw BizException::paramError('请输入会员昵称');
        }
        if (trim((string) ($data['username'] ?? '')) === '' && trim((string) ($data['mobile'] ?? '')) === '') {
            throw BizException::paramError('账号和手机号至少填写一个');
        }
        $this->assertUnique('username', trim((string) ($data['username'] ?? '')));
        $this->assertUnique('mobile', trim((string) ($data['mobile'] ?? '')));

        $now = time();

        return (int) Db::table(self::TABLE)->insertGetId([
            'username' => trim((string) ($data['username'] ?? '')),
            'nickname' => $nickname,
            'avatar' => (string) ($data['avatar'] ?? ''),
            'mobile' => trim((string) ($data['mobile'] ?? '')),
            'email' => (string) ($data['email'] ?? ''),
            'gender' => (int) ($data['gender'] ?? 0),
            'status' => (int) ($data['status'] ?? 1),
            'balance' => (int) ($data['balance'] ?? 0),
            'points' => (int) ($data['points'] ?? 0),
            'remark' => (string) ($data['remark'] ?? ''),
            'create_time' => $now,
            'update_time' => $now,
        ]);
    }

    /** 编辑会员资料。只更新本次提交的字段。 */
    public function update(int $id, array $data): void
    {
        $this->detail($id);

        $updates = [];
        foreach (['username', 'nickname', 'avatar', 'mobile', 'email', 'remark'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = in_array($field, ['username', 'mobile'], true)
                    ? trim((string) $data[$field])
                    : (string) $data[$field];
            }
        }
        foreach (['username', 'mobile'] as $field) {
            if (isset($updates[$field])) {
                $this->assertUnique($field, $updates[$field], $id);
            }
        }
        foreach (['gender', 'status', 'balance', 'points'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (int) $data[$field];
            }
        }

        if ($updates !== []) {
            $updates['update_time'] = time();
            Db::table(self::TABLE)->where('id', $id)->update($updates);
        }
    }

    /**
     * 账号、手机号在未删除的会员里唯一;空值不检查(两者允许只填一个)。
     *
     * @param int $exceptId 编辑时排除自身
     */
    private function assertUnique(string $field, string $value, int $exceptId = 0): void
    {
        if ($value === '') {
            return;
        }

        $exists = Db::table(self::TABLE)
            ->where($field, $value)
            ->whereNull('delete_time')
            ->where('id', '<>', $exceptId)
            ->find();
        if ($exists) {
            throw BizException::conflict($field === 'mobile' ? '手机号已被其他会员使用' : '账号已被其他会员使用');
        }
    }

    /** 软删除会员。 */
    public function delete(int $id): void
    {
        $this->detail($id);
        Db::table(self::TABLE)->where('id', $id)->update(['delete_time' => time()]);
    }
}
