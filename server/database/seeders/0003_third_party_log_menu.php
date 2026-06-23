<?php

/**
 * 第三方请求日志菜单和权限节点(挂在 日志管理 目录下)。
 *
 * 幂等:已存在 system:third-party-log:list 权限时跳过。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'run' => static function (): bool {
        if (Db::table('tl_admin_menu')->where('permission', 'system:third-party-log:list')->find()) {
            return false;
        }

        $logCatalogId = Db::table('tl_admin_menu')
            ->where('type', 'catalog')
            ->where('path', '/system/log')
            ->value('id');

        if (!$logCatalogId) {
            throw new RuntimeException('未找到日志管理目录,请先执行 0001 种子');
        }

        $now = time();

        Db::table('tl_admin_menu')->insert([
            'parent_id' => (int) $logCatalogId, 'type' => 'menu', 'title' => '第三方日志',
            'name' => 'SystemThirdPartyLog', 'path' => '/system/log/third-party', 'component' => 'system/log/third-party',
            'icon' => 'api', 'permission' => 'system:third-party-log:list', 'sort' => 2,
            'visible' => 1, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
        ]);

        return true;
    },
];
