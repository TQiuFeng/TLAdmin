<?php

/**
 * 会员账号、手机号唯一(只约束未删除且非空的记录)。
 *
 * 做法同 20260613_0004:生成列 IF(未删除且非空, 值, NULL) + 唯一索引,
 * 已删除或留空的记录为 NULL,互不冲突。已有重复数据时停下并列出,需先人工处理。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        foreach (['username', 'mobile'] as $column) {
            $genColumn = "{$column}_active";
            $index = "uk_{$column}_active";

            $exists = Db::query(
                'SELECT COUNT(*) AS c FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?',
                ['tl_user', $index]
            );
            if ((int) ($exists[0]['c'] ?? 0) > 0) {
                continue;
            }

            $duplicates = Db::query(
                "SELECT `{$column}` AS v, COUNT(*) AS c FROM `tl_user` "
                . "WHERE `delete_time` IS NULL AND `{$column}` <> '' GROUP BY `{$column}` HAVING c > 1 LIMIT 20"
            );
            if ($duplicates !== []) {
                $list = implode('、', array_map(static fn (array $row): string => "{$row['v']}({$row['c']} 条)", $duplicates));
                throw new RuntimeException("tl_user.{$column} 存在重复的未删除记录,请先处理后再迁移:{$list}");
            }

            Db::execute(
                "ALTER TABLE `tl_user` ADD COLUMN `{$genColumn}` VARCHAR(64) "
                . "GENERATED ALWAYS AS (IF(`delete_time` IS NULL AND `{$column}` <> '', `{$column}`, NULL)) VIRTUAL"
            );
            Db::execute("ALTER TABLE `tl_user` ADD UNIQUE KEY `{$index}` (`{$genColumn}`)");
        }
    },
];
