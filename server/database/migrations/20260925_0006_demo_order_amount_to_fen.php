<?php

/**
 * 演示订单金额改为按分存(框架约定:金额一律存分,页面按元显示)。
 *
 * 原来按整数元存,和演示商品不一致,作为示例会误导。已有数据乘 100;
 * 字段注释改为"(分)",代码生成器据此自动生成金额控件。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        Db::execute("ALTER TABLE `tl_demo_order` MODIFY `amount` int unsigned NOT NULL DEFAULT 0 COMMENT '订单金额(分)'");
        Db::execute('UPDATE `tl_demo_order` SET `amount` = `amount` * 100');
    },
];
