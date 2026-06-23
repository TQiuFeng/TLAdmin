<?php

/**
 * 修复软删除表的唯一约束:让 username/code 的唯一性只作用于未删除的记录。
 *
 * 原本 uk_username/uk_code 对全表生效,导致软删除一条记录后无法再创建同名记录
 * (应用层用 whereNull('delete_time') 检查通过,却在写入时撞 DB 唯一键报 500)。
 * 改为基于生成列 IF(delete_time IS NULL, col, NULL):未删除记录值为本身(唯一),
 * 已删除记录值为 NULL(MySQL 唯一索引中 NULL 互不冲突,可重复)。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'up' => static function (): void {
        $tables = [
            ['table' => 'tl_admin_user', 'column' => 'username', 'old_index' => 'uk_username'],
            ['table' => 'tl_admin_role', 'column' => 'code', 'old_index' => 'uk_code'],
            ['table' => 'tl_admin_post', 'column' => 'code', 'old_index' => 'uk_code'],
            ['table' => 'tl_dict_type', 'column' => 'code', 'old_index' => 'uk_code'],
        ];

        foreach ($tables as $item) {
            $table = $item['table'];
            $column = $item['column'];
            $genColumn = "{$column}_active";
            $newIndex = "uk_{$column}_active";

            // 幂等:已迁移过则跳过
            $exists = Db::query(
                "SELECT COUNT(*) AS c FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?",
                [$table, $newIndex]
            );
            if ((int) ($exists[0]['c'] ?? 0) > 0) {
                continue;
            }

            Db::execute("ALTER TABLE `{$table}` DROP INDEX `{$item['old_index']}`");
            Db::execute(
                "ALTER TABLE `{$table}` ADD COLUMN `{$genColumn}` VARCHAR(64) "
                . "GENERATED ALWAYS AS (IF(`delete_time` IS NULL, `{$column}`, NULL)) VIRTUAL"
            );
            Db::execute("ALTER TABLE `{$table}` ADD UNIQUE KEY `{$newIndex}` (`{$genColumn}`)");
        }
    },
];
