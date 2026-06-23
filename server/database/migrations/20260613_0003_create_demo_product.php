<?php

/**
 * 演示商品表(代码生成器演示用)。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        Db::execute(<<<SQL
CREATE TABLE IF NOT EXISTS `tl_demo_product` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称',
    `price` int unsigned NOT NULL DEFAULT 0 COMMENT '价格(分)',
    `stock` int unsigned NOT NULL DEFAULT 0 COMMENT '库存',
    `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态:1启用 0禁用',
    `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
    `create_time` int unsigned NOT NULL DEFAULT 0,
    `update_time` int unsigned NOT NULL DEFAULT 0,
    `delete_time` int unsigned DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示商品';
SQL);
    },
];
