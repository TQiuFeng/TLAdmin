<?php

/**
 * 演示公告表(代码生成器演示:生效/失效时间、置顶)。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        Db::execute(<<<SQL
CREATE TABLE IF NOT EXISTS `tl_demo_notice` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(150) NOT NULL DEFAULT '' COMMENT '公告标题',
    `content` text COMMENT '公告内容',
    `is_top` tinyint NOT NULL DEFAULT 0 COMMENT '置顶:1是 0否',
    `start_time` int unsigned NOT NULL DEFAULT 0 COMMENT '生效时间',
    `end_time` int unsigned NOT NULL DEFAULT 0 COMMENT '失效时间',
    `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态:1启用 0禁用',
    `create_time` int unsigned NOT NULL DEFAULT 0,
    `update_time` int unsigned NOT NULL DEFAULT 0,
    `delete_time` int unsigned DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示公告';
SQL);
    },
];
