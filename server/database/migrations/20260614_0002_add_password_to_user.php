<?php

/**
 * 会员表 tl_user 增加登录密码字段(C 端注册/登录用,bcrypt 存储)。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        Db::execute("ALTER TABLE `tl_user` ADD COLUMN `password` varchar(255) NOT NULL DEFAULT '' COMMENT '登录密码(bcrypt)' AFTER `username`");
    },
];
