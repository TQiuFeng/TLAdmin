<?php

/**
 * 演示客户表(代码生成器演示:多条件搜索、数字、跟进时间)。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        Db::execute(<<<SQL
CREATE TABLE IF NOT EXISTS `tl_demo_customer` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL DEFAULT '' COMMENT '客户名称',
    `contact` varchar(50) NOT NULL DEFAULT '' COMMENT '联系人',
    `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
    `source` varchar(30) NOT NULL DEFAULT '' COMMENT '客户来源',
    `level` tinyint unsigned NOT NULL DEFAULT 1 COMMENT '客户等级(1-5)',
    `next_follow_time` int unsigned NOT NULL DEFAULT 0 COMMENT '下次跟进',
    `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态:1启用 0禁用',
    `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
    `create_time` int unsigned NOT NULL DEFAULT 0,
    `update_time` int unsigned NOT NULL DEFAULT 0,
    `delete_time` int unsigned DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示客户';
SQL);
    },
];
