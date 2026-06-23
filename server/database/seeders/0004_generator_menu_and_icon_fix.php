<?php

/**
 * 代码生成器菜单 + 修正历史菜单图标(对已部署库幂等)。
 *
 * - 新增"代码生成器"菜单(权限 system:generator:use),挂在系统管理目录下。
 * - 修正岗位(contacts 是无效图标名)、操作/登录/第三方日志(原本无图标)。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'run' => static function (): bool {
        $now = time();

        // 修正历史图标(幂等:值已正确则 update 不变)
        $iconFixes = [
            '/system/post' => 'usergroup',
            '/system/log/operation' => 'history',
            '/system/log/login' => 'login',
            '/system/log/third-party' => 'api',
        ];
        foreach ($iconFixes as $path => $icon) {
            Db::table('tl_admin_menu')->where('path', $path)->update(['icon' => $icon, 'update_time' => $now]);
        }

        // 代码生成器菜单(已存在则跳过)
        if (Db::table('tl_admin_menu')->where('permission', 'system:generator:use')->find()) {
            return true;
        }

        $systemId = Db::table('tl_admin_menu')
            ->where('type', 'catalog')
            ->where('path', '/system')
            ->value('id');

        if (!$systemId) {
            throw new RuntimeException('未找到系统管理目录,请先执行 0001 种子');
        }

        Db::table('tl_admin_menu')->insert([
            'parent_id' => (int) $systemId, 'type' => 'menu', 'title' => '代码生成器',
            'name' => 'SystemGenerator', 'path' => '/system/generator', 'component' => 'system/generator/index',
            'icon' => 'code', 'permission' => 'system:generator:use', 'sort' => 8,
            'visible' => 1, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
        ]);

        return true;
    },
];
