<?php

/**
 * 演示文章表(代码生成器演示:搜索、开关、时间、长文本)。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        Db::execute(<<<SQL
CREATE TABLE IF NOT EXISTS `tl_demo_article` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(150) NOT NULL DEFAULT '' COMMENT '文章标题',
    `author` varchar(50) NOT NULL DEFAULT '' COMMENT '作者',
    `category` varchar(50) NOT NULL DEFAULT '' COMMENT '分类',
    `summary` varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
    `content` text COMMENT '正文',
    `views` int unsigned NOT NULL DEFAULT 0 COMMENT '阅读量',
    `is_top` tinyint NOT NULL DEFAULT 0 COMMENT '置顶:1是 0否',
    `publish_time` int unsigned NOT NULL DEFAULT 0 COMMENT '发布时间',
    `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态:1启用 0禁用',
    `create_time` int unsigned NOT NULL DEFAULT 0,
    `update_time` int unsigned NOT NULL DEFAULT 0,
    `delete_time` int unsigned DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示文章';
SQL);
    },
];
