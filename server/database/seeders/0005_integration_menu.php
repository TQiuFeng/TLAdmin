<?php

/**
 * 插件配置菜单(第三方能力:支付/微信/短信/存储),挂在系统管理目录下。
 *
 * 复用 system:config:list / system:config:update 权限(与后端 integrations 接口一致)。
 * 幂等:菜单已存在则跳过。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'run' => static function (): bool {
        if (Db::table('tl_admin_menu')->where('path', '/system/integration')->find()) {
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

        Db::table('tl_admin_menu')->insert([
            'parent_id' => (int) $systemId, 'type' => 'menu', 'title' => '插件配置',
            'name' => 'SystemIntegration', 'path' => '/system/integration', 'component' => 'system/integration/index',
            'icon' => 'app', 'permission' => 'system:config:list', 'sort' => 9,
            'visible' => 1, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
        ]);

        return true;
    },
];
