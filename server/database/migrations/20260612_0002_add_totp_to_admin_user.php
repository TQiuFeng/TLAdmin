<?php

/**
 * 管理员表增加 Google Authenticator TOTP 字段。
 *
 * totp_secret 加密存储(enc:v1: 前缀),totp_enabled 标记是否已绑定。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        Db::execute(<<<SQL
            ALTER TABLE `tl_admin_user`
                ADD COLUMN `totp_secret` varchar(255) NOT NULL DEFAULT '' COMMENT 'TOTP 密钥(加密存储)' AFTER `password`,
                ADD COLUMN `totp_enabled` tinyint NOT NULL DEFAULT 0 COMMENT '是否已绑定动态验证码' AFTER `totp_secret`
            SQL);
    },
];
