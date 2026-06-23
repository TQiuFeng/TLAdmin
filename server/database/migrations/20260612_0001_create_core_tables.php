<?php

/**
 * 核心系统表迁移:管理员、角色、菜单、部门、岗位、配置、字典、日志、附件。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_admin_user` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `username` varchar(64) NOT NULL COMMENT '登录账号',
                `password` varchar(255) NOT NULL COMMENT '密码哈希',
                `nickname` varchar(64) NOT NULL DEFAULT '' COMMENT '昵称',
                `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
                `email` varchar(128) NOT NULL DEFAULT '' COMMENT '邮箱',
                `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '手机号',
                `dept_id` bigint unsigned NOT NULL DEFAULT 0 COMMENT '部门',
                `is_super` tinyint NOT NULL DEFAULT 0 COMMENT '超级管理员',
                `status` tinyint NOT NULL DEFAULT 1 COMMENT '1启用 0禁用',
                `last_login_time` int unsigned NOT NULL DEFAULT 0,
                `last_login_ip` varchar(64) NOT NULL DEFAULT '',
                `remark` varchar(500) NOT NULL DEFAULT '',
                `create_time` int unsigned NOT NULL DEFAULT 0,
                `update_time` int unsigned NOT NULL DEFAULT 0,
                `delete_time` int unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_admin_role` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(64) NOT NULL COMMENT '角色名',
                `code` varchar(64) NOT NULL COMMENT '角色编码',
                `data_scope` varchar(20) NOT NULL DEFAULT 'self' COMMENT 'all/self/dept/dept_tree/custom',
                `sort` int NOT NULL DEFAULT 0,
                `status` tinyint NOT NULL DEFAULT 1,
                `remark` varchar(500) NOT NULL DEFAULT '',
                `create_time` int unsigned NOT NULL DEFAULT 0,
                `update_time` int unsigned NOT NULL DEFAULT 0,
                `delete_time` int unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_code` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_admin_menu` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `parent_id` bigint unsigned NOT NULL DEFAULT 0,
                `type` varchar(20) NOT NULL DEFAULT 'menu' COMMENT 'catalog/menu/button/api',
                `title` varchar(64) NOT NULL COMMENT '显示名称',
                `name` varchar(64) NOT NULL DEFAULT '' COMMENT '路由名',
                `path` varchar(191) NOT NULL DEFAULT '' COMMENT '路由路径',
                `component` varchar(191) NOT NULL DEFAULT '' COMMENT '前端组件',
                `icon` varchar(64) NOT NULL DEFAULT '',
                `permission` varchar(128) NOT NULL DEFAULT '' COMMENT '权限标识 如 system:user:list',
                `sort` int NOT NULL DEFAULT 0,
                `visible` tinyint NOT NULL DEFAULT 1 COMMENT '菜单是否显示',
                `status` tinyint NOT NULL DEFAULT 1,
                `create_time` int unsigned NOT NULL DEFAULT 0,
                `update_time` int unsigned NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `idx_parent` (`parent_id`),
                KEY `idx_permission` (`permission`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='菜单和权限节点'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_admin_user_role` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint unsigned NOT NULL,
                `role_id` bigint unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_user_role` (`user_id`, `role_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员角色关系'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_admin_role_menu` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `role_id` bigint unsigned NOT NULL,
                `menu_id` bigint unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_role_menu` (`role_id`, `menu_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色菜单权限关系'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_admin_role_dept` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `role_id` bigint unsigned NOT NULL,
                `dept_id` bigint unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_role_dept` (`role_id`, `dept_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色自定义数据权限部门'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_admin_dept` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `parent_id` bigint unsigned NOT NULL DEFAULT 0,
                `name` varchar(64) NOT NULL,
                `leader` varchar(64) NOT NULL DEFAULT '',
                `phone` varchar(32) NOT NULL DEFAULT '',
                `email` varchar(128) NOT NULL DEFAULT '',
                `sort` int NOT NULL DEFAULT 0,
                `status` tinyint NOT NULL DEFAULT 1,
                `create_time` int unsigned NOT NULL DEFAULT 0,
                `update_time` int unsigned NOT NULL DEFAULT 0,
                `delete_time` int unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_parent` (`parent_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='部门'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_admin_post` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(64) NOT NULL,
                `code` varchar(64) NOT NULL,
                `sort` int NOT NULL DEFAULT 0,
                `status` tinyint NOT NULL DEFAULT 1,
                `remark` varchar(500) NOT NULL DEFAULT '',
                `create_time` int unsigned NOT NULL DEFAULT 0,
                `update_time` int unsigned NOT NULL DEFAULT 0,
                `delete_time` int unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_code` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='岗位'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_admin_user_post` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint unsigned NOT NULL,
                `post_id` bigint unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_user_post` (`user_id`, `post_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员岗位关系'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_config` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `group` varchar(64) NOT NULL DEFAULT 'system' COMMENT '配置分组',
                `name` varchar(128) NOT NULL DEFAULT '' COMMENT '配置名称',
                `key` varchar(128) NOT NULL COMMENT '配置键 如 api.response_format',
                `value` text COMMENT '配置值',
                `type` varchar(20) NOT NULL DEFAULT 'text' COMMENT 'text/number/switch/json/image/file/select',
                `options` text COMMENT '可选项 JSON',
                `is_encrypted` tinyint NOT NULL DEFAULT 0 COMMENT '值是否加密存储',
                `sort` int NOT NULL DEFAULT 0,
                `remark` varchar(500) NOT NULL DEFAULT '',
                `create_time` int unsigned NOT NULL DEFAULT 0,
                `update_time` int unsigned NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_key` (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_dict_type` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(64) NOT NULL COMMENT '字典名称',
                `code` varchar(64) NOT NULL COMMENT '字典编码',
                `status` tinyint NOT NULL DEFAULT 1,
                `remark` varchar(500) NOT NULL DEFAULT '',
                `create_time` int unsigned NOT NULL DEFAULT 0,
                `update_time` int unsigned NOT NULL DEFAULT 0,
                `delete_time` int unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_code` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='字典类型'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_dict_data` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `type_code` varchar(64) NOT NULL COMMENT '字典类型编码',
                `label` varchar(128) NOT NULL COMMENT '显示标签',
                `value` varchar(128) NOT NULL COMMENT '字典值',
                `sort` int NOT NULL DEFAULT 0,
                `status` tinyint NOT NULL DEFAULT 1,
                `remark` varchar(500) NOT NULL DEFAULT '',
                `create_time` int unsigned NOT NULL DEFAULT 0,
                `update_time` int unsigned NOT NULL DEFAULT 0,
                `delete_time` int unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_type` (`type_code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='字典数据'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_login_log` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint unsigned NOT NULL DEFAULT 0,
                `username` varchar(64) NOT NULL DEFAULT '',
                `ip` varchar(64) NOT NULL DEFAULT '',
                `location` varchar(128) NOT NULL DEFAULT '' COMMENT 'IP 归属地',
                `user_agent` varchar(500) NOT NULL DEFAULT '',
                `status` tinyint NOT NULL DEFAULT 1 COMMENT '1成功 0失败',
                `message` varchar(255) NOT NULL DEFAULT '' COMMENT '失败原因',
                `create_time` int unsigned NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `idx_username` (`username`),
                KEY `idx_create_time` (`create_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='登录日志'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_operation_log` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint unsigned NOT NULL DEFAULT 0,
                `username` varchar(64) NOT NULL DEFAULT '',
                `method` varchar(10) NOT NULL DEFAULT '',
                `path` varchar(191) NOT NULL DEFAULT '',
                `permission` varchar(128) NOT NULL DEFAULT '' COMMENT '权限标识',
                `ip` varchar(64) NOT NULL DEFAULT '',
                `user_agent` varchar(500) NOT NULL DEFAULT '',
                `params` text COMMENT '请求参数摘要(敏感字段脱敏)',
                `status_code` int NOT NULL DEFAULT 0,
                `duration_ms` int NOT NULL DEFAULT 0,
                `request_id` varchar(64) NOT NULL DEFAULT '',
                `create_time` int unsigned NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `idx_user` (`user_id`),
                KEY `idx_create_time` (`create_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志'
            SQL);

        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_attachment` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(191) NOT NULL COMMENT '原始文件名',
                `path` varchar(191) NOT NULL COMMENT '存储路径',
                `disk` varchar(32) NOT NULL DEFAULT 'local' COMMENT '存储磁盘',
                `url` varchar(500) NOT NULL DEFAULT '',
                `mime` varchar(128) NOT NULL DEFAULT '',
                `ext` varchar(32) NOT NULL DEFAULT '',
                `size` bigint unsigned NOT NULL DEFAULT 0 COMMENT '字节',
                `sha1` varchar(64) NOT NULL DEFAULT '',
                `uploader_id` bigint unsigned NOT NULL DEFAULT 0,
                `create_time` int unsigned NOT NULL DEFAULT 0,
                `delete_time` int unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_uploader` (`uploader_id`),
                KEY `idx_sha1` (`sha1`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='附件'
            SQL);
    },
];
