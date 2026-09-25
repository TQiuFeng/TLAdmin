<?php

/**
 * 演示订单表(代码生成器演示:精确搜索、支付开关、无状态字段)。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        Db::execute(<<<SQL
CREATE TABLE IF NOT EXISTS `tl_demo_order` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `order_no` varchar(32) NOT NULL DEFAULT '' COMMENT '订单号',
    `customer_name` varchar(100) NOT NULL DEFAULT '' COMMENT '客户',
    `goods_name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品',
    `quantity` int unsigned NOT NULL DEFAULT 1 COMMENT '数量',
    `amount` int unsigned NOT NULL DEFAULT 0 COMMENT '订单金额(元)',
    `is_paid` tinyint NOT NULL DEFAULT 0 COMMENT '已支付:1是 0否',
    `pay_time` int unsigned NOT NULL DEFAULT 0 COMMENT '支付时间',
    `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
    `create_time` int unsigned NOT NULL DEFAULT 0,
    `update_time` int unsigned NOT NULL DEFAULT 0,
    `delete_time` int unsigned DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示订单';
SQL);
    },
];
