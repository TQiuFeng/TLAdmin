<?php

/**
 * 演示订单菜单和权限节点(代码生成器生成,幂等)。
 * Author: qiufeng(代码生成器)
 */

use think\facade\Db;

return [
    'run' => static function (): bool {
        if (Db::table('tl_admin_menu')->where('permission', 'demo-order:list')->find()) {
            return false;
        }

        $now = time();

        $menuId = Db::table('tl_admin_menu')->insertGetId([
            'parent_id' => 0, 'type' => 'menu', 'title' => '演示订单',
            'name' => 'DemoOrder', 'path' => '/gen/demo-order', 'component' => 'gen/demo-order/index',
            'icon' => 'view-module', 'permission' => 'demo-order:list', 'sort' => 90,
            'visible' => 1, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
        ]);

        foreach ([['新增', 'create'], ['编辑', 'update'], ['删除', 'delete']] as $i => [$title, $action]) {
            Db::table('tl_admin_menu')->insert([
                'parent_id' => $menuId, 'type' => 'button', 'title' => $title,
                'name' => '', 'path' => '', 'component' => '', 'icon' => '',
                'permission' => "demo-order:{$action}", 'sort' => $i,
                'visible' => 1, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
            ]);
        }

        return true;
    },
];
