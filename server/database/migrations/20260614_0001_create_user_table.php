<?php

/**
 * 会员用户表 tl_user(C 端用户,区别于管理后台账号 tl_admin_user)。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        Db::execute(<<<SQL
CREATE TABLE IF NOT EXISTS `tl_user` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `username` varchar(64) NOT NULL DEFAULT '' COMMENT '会员账号',
    `nickname` varchar(64) NOT NULL DEFAULT '' COMMENT '昵称',
    `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
    `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
    `email` varchar(128) NOT NULL DEFAULT '' COMMENT '邮箱',
    `gender` tinyint NOT NULL DEFAULT 0 COMMENT '性别:0未知 1男 2女',
    `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态:1正常 0禁用',
    `balance` int NOT NULL DEFAULT 0 COMMENT '余额(分)',
    `points` int NOT NULL DEFAULT 0 COMMENT '积分',
    `register_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '注册IP',
    `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
    `last_login_time` int unsigned NOT NULL DEFAULT 0 COMMENT '最后登录时间',
    `create_time` int unsigned NOT NULL DEFAULT 0,
    `update_time` int unsigned NOT NULL DEFAULT 0,
    `delete_time` int unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_mobile` (`mobile`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员用户';
SQL);
    },
];
