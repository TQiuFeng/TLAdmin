<?php

/**
 * 会员后台管理:账号、手机号在未删除会员中唯一。测试数据用随机账号,结束后物理删除。
 * Author: qiufeng
 */

use app\common\exception\BizException;
use app\common\service\member\UserService;
use think\facade\Db;

/** 建一个测试会员,用例结束物理删除 */
$createMember = static function (TestContext $t, array $data): int {
    $id = $t->make(UserService::class)->create($data + ['nickname' => '测试会员']);
    $t->defer(static fn () => Db::table('tl_user')->where('id', $id)->delete());

    return $id;
};

$random = static fn (string $prefix): string => $prefix . bin2hex(random_bytes(4));

return [
    '重复账号返回 40900' => static function (TestContext $t) use ($createMember, $random): void {
        $t->needsDb();
        $username = $random('t_user_');
        $createMember($t, ['username' => $username]);

        $e = $t->throws(BizException::class, fn () => $createMember($t, ['username' => $username]));
        $t->same(40900, $e->bizCode());
    },

    '重复手机号返回 40900' => static function (TestContext $t) use ($createMember): void {
        $t->needsDb();
        $mobile = '199' . random_int(10000000, 99999999);
        $createMember($t, ['mobile' => $mobile]);

        $e = $t->throws(BizException::class, fn () => $createMember($t, ['mobile' => $mobile]));
        $t->same('手机号已被其他会员使用', $e->getMessage());
    },

    '编辑成别人的账号被拒,编辑自己不受影响' => static function (TestContext $t) use ($createMember, $random): void {
        $t->needsDb();
        $a = $random('t_user_');
        $createMember($t, ['username' => $a]);
        $bId = $createMember($t, ['username' => $random('t_user_')]);

        $service = $t->make(UserService::class);
        $t->throws(BizException::class, fn () => $service->update($bId, ['username' => $a]));
        $service->update($bId, ['nickname' => '改个昵称']);
        $t->same('改个昵称', Db::table('tl_user')->where('id', $bId)->value('nickname'));
    },

    '账号留空可以有多个;软删除后账号可复用' => static function (TestContext $t) use ($createMember, $random): void {
        $t->needsDb();
        $createMember($t, ['username' => '', 'mobile' => '199' . random_int(10000000, 99999999)]);
        $createMember($t, ['username' => '', 'mobile' => '199' . random_int(10000000, 99999999)]);

        $username = $random('t_user_');
        $id = $createMember($t, ['username' => $username]);
        $t->make(UserService::class)->delete($id);
        $createMember($t, ['username' => $username]);
        $t->true(true, '');
    },
];
