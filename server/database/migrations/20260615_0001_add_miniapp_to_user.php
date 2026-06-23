<?php

/**
 * 会员表 tl_user 增加微信小程序身份字段。
 *
 * wx_openid 唯一性沿用项目软删除感知方案:生成列 wx_openid_active 仅在
 * 未删除且 openid 非空时取值,否则为 NULL(MySQL 唯一索引中 NULL 互不冲突),
 * 这样未绑定小程序的会员(openid 为空)和已软删除的会员都不会撞唯一键。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        Db::execute("ALTER TABLE `tl_user` ADD COLUMN `wx_openid` varchar(64) NOT NULL DEFAULT '' COMMENT '微信小程序 openid' AFTER `mobile`");
        Db::execute("ALTER TABLE `tl_user` ADD COLUMN `wx_unionid` varchar(64) NOT NULL DEFAULT '' COMMENT '微信开放平台 unionid' AFTER `wx_openid`");
        Db::execute(
            "ALTER TABLE `tl_user` ADD COLUMN `wx_openid_active` VARCHAR(64) "
            . "GENERATED ALWAYS AS (IF(`delete_time` IS NULL AND `wx_openid` <> '', `wx_openid`, NULL)) VIRTUAL"
        );
        Db::execute("ALTER TABLE `tl_user` ADD UNIQUE KEY `uk_wx_openid_active` (`wx_openid_active`)");
    },
];
