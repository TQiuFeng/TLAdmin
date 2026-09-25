<?php

/**
 * 仪表盘:统计按权限裁剪。
 * Author: qiufeng
 */

use app\common\service\system\DashboardService;
use think\facade\Db;

return [
    '超管看到全部统计卡片' => static function (TestContext $t): void {
        $t->needsDb();
        $superId = (int) Db::table('tl_admin_user')->where('is_super', 1)->whereNull('delete_time')->value('id');
        if ($superId === 0) {
            throw new TestSkipped('没有超管账号');
        }

        $overview = $t->make(DashboardService::class)->overview($superId);
        $keys = array_column($overview['stats'], 'key');
        foreach (['admins', 'roles', 'members'] as $key) {
            $t->true(in_array($key, $keys, true), "缺少 {$key}");
        }
        $admins = (int) Db::table('tl_admin_user')->whereNull('delete_time')->count();
        $t->same($admins, $overview['stats'][array_search('admins', $keys, true)]['value']);
    },

    '没有任何权限的账号什么统计都拿不到' => static function (TestContext $t): void {
        $t->needsDb();
        $now = time();
        $id = (int) Db::table('tl_admin_user')->insertGetId([
            'username' => 't_dash_' . bin2hex(random_bytes(4)),
            'password' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT),
            'nickname' => '仪表盘测试',
            'is_super' => 0,
            'status' => 1,
            'create_time' => $now,
            'update_time' => $now,
        ]);
        $t->defer(static fn () => Db::table('tl_admin_user')->where('id', $id)->delete());

        $overview = $t->make(DashboardService::class)->overview($id);
        $t->same(['stats' => []], $overview);
    },
];
