<?php

/**
 * 附件管理菜单和权限节点。
 *
 * 幂等:已存在 system:attachment:list 权限时跳过。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'run' => static function (): bool {
        if (Db::table('tl_admin_menu')->where('permission', 'system:attachment:list')->find()) {
            return false;
        }

        $systemId = Db::table('tl_admin_menu')
            ->where('type', 'catalog')
            ->where('path', '/system')
            ->value('id');

        if (!$systemId) {
            throw new RuntimeException('未找到系统管理目录,请先执行 0001 种子');
        }

        $now = time();

        $menuId = Db::table('tl_admin_menu')->insertGetId([
            'parent_id' => (int) $systemId, 'type' => 'menu', 'title' => '附件管理',
            'name' => 'SystemAttachment', 'path' => '/system/attachment', 'component' => 'system/attachment/index',
            'icon' => 'folder', 'permission' => 'system:attachment:list', 'sort' => 6,
            'visible' => 1, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
        ]);

        Db::table('tl_admin_menu')->insert([
            'parent_id' => $menuId, 'type' => 'button', 'title' => '删除',
            'name' => '', 'path' => '', 'component' => '', 'icon' => '',
            'permission' => 'system:attachment:delete', 'sort' => 0,
            'visible' => 1, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
        ]);

        return true;
    },
];
