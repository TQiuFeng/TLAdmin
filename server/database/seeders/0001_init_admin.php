<?php

/**
 * 初始化种子:超级管理员、角色、部门、菜单权限树、字典、系统配置。
 *
 * 幂等:已存在 admin 账号时跳过。
 * 默认账号 admin / admin123456,生产环境部署后必须修改。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'run' => static function (): bool {
        if (Db::table('tl_admin_user')->where('username', 'admin')->find()) {
            return false;
        }

        $now = time();

        Db::startTrans();
        try {
            $deptId = Db::table('tl_admin_dept')->insertGetId([
                'parent_id' => 0, 'name' => '总公司', 'sort' => 0, 'status' => 1,
                'create_time' => $now, 'update_time' => $now,
            ]);

            $superRoleId = Db::table('tl_admin_role')->insertGetId([
                'name' => '超级管理员', 'code' => 'super_admin', 'data_scope' => 'all',
                'sort' => 0, 'status' => 1, 'remark' => '系统内置角色,拥有全部权限',
                'create_time' => $now, 'update_time' => $now,
            ]);
            $commonRoleId = Db::table('tl_admin_role')->insertGetId([
                'name' => '普通管理员', 'code' => 'common', 'data_scope' => 'self',
                'sort' => 1, 'status' => 1, 'remark' => '默认角色,只能查看',
                'create_time' => $now, 'update_time' => $now,
            ]);

            $adminId = Db::table('tl_admin_user')->insertGetId([
                'username' => 'admin',
                'password' => password_hash('admin123456', PASSWORD_BCRYPT),
                'nickname' => '超级管理员',
                'dept_id' => $deptId,
                'is_super' => 1,
                'status' => 1,
                'create_time' => $now,
                'update_time' => $now,
            ]);

            Db::table('tl_admin_user_role')->insert(['user_id' => $adminId, 'role_id' => $superRoleId]);

            // 菜单树:[type, title, name, path, component, icon, permission, children]
            $menu = static function (
                int $parentId,
                string $type,
                string $title,
                string $name = '',
                string $path = '',
                string $component = '',
                string $icon = '',
                string $permission = '',
                int $sort = 0
            ) use ($now): int {
                return Db::table('tl_admin_menu')->insertGetId([
                    'parent_id' => $parentId, 'type' => $type, 'title' => $title,
                    'name' => $name, 'path' => $path, 'component' => $component,
                    'icon' => $icon, 'permission' => $permission, 'sort' => $sort,
                    'visible' => 1, 'status' => 1,
                    'create_time' => $now, 'update_time' => $now,
                ]);
            };
            $buttons = static function (int $parentId, string $module, array $actions) use ($menu): void {
                $labels = [
                    'create' => '新增', 'update' => '编辑', 'delete' => '删除',
                    'reset-password' => '重置密码', 'assign-role' => '分配角色', 'assign-menu' => '分配权限',
                ];
                $sort = 0;
                foreach ($actions as $action) {
                    $menu($parentId, 'button', $labels[$action] ?? $action, '', '', '', '', "system:{$module}:{$action}", $sort++);
                }
            };

            $dashboard = $menu(0, 'menu', '仪表盘', 'Dashboard', '/dashboard', 'dashboard/index', 'dashboard', '', 0);

            $system = $menu(0, 'catalog', '系统管理', 'System', '/system', '', 'setting', '', 1);

            $user = $menu($system, 'menu', '管理员管理', 'SystemUser', '/system/user', 'system/user/index', 'user', 'system:user:list', 0);
            $buttons($user, 'user', ['create', 'update', 'delete', 'reset-password', 'assign-role']);

            $role = $menu($system, 'menu', '角色管理', 'SystemRole', '/system/role', 'system/role/index', 'user-safety', 'system:role:list', 1);
            $buttons($role, 'role', ['create', 'update', 'delete', 'assign-menu']);

            $menuPage = $menu($system, 'menu', '菜单管理', 'SystemMenu', '/system/menu', 'system/menu/index', 'menu-fold', 'system:menu:list', 2);
            $buttons($menuPage, 'menu', ['create', 'update', 'delete']);

            $dept = $menu($system, 'menu', '部门管理', 'SystemDept', '/system/dept', 'system/dept/index', 'sitemap', 'system:dept:list', 3);
            $buttons($dept, 'dept', ['create', 'update', 'delete']);

            $post = $menu($system, 'menu', '岗位管理', 'SystemPost', '/system/post', 'system/post/index', 'usergroup', 'system:post:list', 4);
            $buttons($post, 'post', ['create', 'update', 'delete']);

            $dict = $menu($system, 'menu', '字典管理', 'SystemDict', '/system/dict', 'system/dict/index', 'book', 'system:dict:list', 5);
            $buttons($dict, 'dict', ['create', 'update', 'delete']);

            $config = $menu($system, 'menu', '系统配置', 'SystemConfig', '/system/config', 'system/config/index', 'tools', 'system:config:list', 6);
            $menu($config, 'button', '保存配置', '', '', '', '', 'system:config:update', 0);

            $logCatalog = $menu($system, 'catalog', '日志管理', 'SystemLog', '/system/log', '', 'file-paste', '', 7);
            $menu($logCatalog, 'menu', '操作日志', 'OperationLog', '/system/log/operation', 'system/log/operation', 'history', 'system:operation-log:list', 0);
            $menu($logCatalog, 'menu', '登录日志', 'LoginLog', '/system/log/login', 'system/log/login', 'login', 'system:login-log:list', 1);

            // 普通管理员角色默认只授予仪表盘和各列表页
            $listMenuIds = Db::table('tl_admin_menu')
                ->where('type', 'in', ['catalog', 'menu'])
                ->column('id');
            foreach ($listMenuIds as $menuId) {
                Db::table('tl_admin_role_menu')->insert([
                    'role_id' => $commonRoleId, 'menu_id' => (int) $menuId,
                ]);
            }

            // 内置字典
            $dictTypes = [
                ['name' => '系统状态', 'code' => 'sys_status', 'items' => [['启用', '1'], ['禁用', '0']]],
                ['name' => '是否', 'code' => 'sys_yes_no', 'items' => [['是', '1'], ['否', '0']]],
                ['name' => '菜单类型', 'code' => 'menu_type', 'items' => [['目录', 'catalog'], ['菜单', 'menu'], ['按钮', 'button'], ['接口', 'api']]],
                ['name' => '数据权限', 'code' => 'data_scope', 'items' => [['全部数据', 'all'], ['仅本人', 'self'], ['本部门', 'dept'], ['本部门及下级', 'dept_tree'], ['自定义', 'custom']]],
            ];
            foreach ($dictTypes as $type) {
                Db::table('tl_dict_type')->insert([
                    'name' => $type['name'], 'code' => $type['code'], 'status' => 1,
                    'create_time' => $now, 'update_time' => $now,
                ]);
                foreach ($type['items'] as $sort => [$label, $value]) {
                    Db::table('tl_dict_data')->insert([
                        'type_code' => $type['code'], 'label' => $label, 'value' => $value,
                        'sort' => $sort, 'status' => 1,
                        'create_time' => $now, 'update_time' => $now,
                    ]);
                }
            }

            // 系统配置
            Db::table('tl_config')->insert([
                'group' => 'api', 'name' => 'API 默认响应格式', 'key' => 'api.response_format',
                'value' => 'json', 'type' => 'select',
                'options' => json_encode([['label' => 'JSON', 'value' => 'json'], ['label' => 'XML', 'value' => 'xml']], JSON_UNESCAPED_UNICODE),
                'sort' => 0, 'remark' => '未在请求中指定格式时使用',
                'create_time' => $now, 'update_time' => $now,
            ]);

            Db::commit();
        } catch (Throwable $e) {
            Db::rollback();
            throw $e;
        }

        return true;
    },
];
