<?php

/**
 * 会员管理菜单 + 权限节点 + 几条测试会员数据(幂等)。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'run' => static function (): bool {
        if (Db::table('tl_admin_menu')->where('permission', 'member:user:list')->find()) {
            return false;
        }

        $now = time();

        // 顶级"会员管理"目录
        $catalogId = Db::table('tl_admin_menu')->insertGetId([
            'parent_id' => 0, 'type' => 'catalog', 'title' => '会员管理',
            'name' => 'Member', 'path' => '/member', 'component' => '',
            'icon' => 'usergroup', 'permission' => '', 'sort' => 5,
            'visible' => 1, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
        ]);

        // 会员列表菜单
        $menuId = Db::table('tl_admin_menu')->insertGetId([
            'parent_id' => $catalogId, 'type' => 'menu', 'title' => '会员列表',
            'name' => 'MemberUser', 'path' => '/member/user', 'component' => 'member/user/index',
            'icon' => 'user', 'permission' => 'member:user:list', 'sort' => 0,
            'visible' => 1, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
        ]);

        foreach ([['新增', 'create'], ['编辑', 'update'], ['删除', 'delete']] as $i => [$title, $action]) {
            Db::table('tl_admin_menu')->insert([
                'parent_id' => $menuId, 'type' => 'button', 'title' => $title,
                'name' => '', 'path' => '', 'component' => '', 'icon' => '',
                'permission' => "member:user:{$action}", 'sort' => $i,
                'visible' => 1, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
            ]);
        }

        // 测试会员数据
        if (Db::table('tl_user')->count() === 0) {
            $rows = [];
            $names = ['张小明', '李华', '王芳', '赵磊', '陈静'];
            foreach ($names as $idx => $name) {
                $rows[] = [
                    'username' => 'user' . (1001 + $idx),
                    'nickname' => $name,
                    'avatar' => '',
                    'mobile' => '138' . str_pad((string) (10000000 + $idx), 8, '0', STR_PAD_LEFT),
                    'email' => 'user' . (1001 + $idx) . '@example.com',
                    'gender' => $idx % 3,
                    'status' => $idx === 4 ? 0 : 1,
                    'balance' => ($idx + 1) * 5000,
                    'points' => ($idx + 1) * 100,
                    'register_ip' => '127.0.0.1',
                    'remark' => '',
                    'last_login_time' => $now - $idx * 3600,
                    'create_time' => $now - $idx * 86400,
                    'update_time' => $now,
                ];
            }
            Db::table('tl_user')->insertAll($rows);
        }

        return true;
    },
];
