<?php

namespace app\common\database;

use think\facade\Db;

/**
 * 迁移执行器。
 *
 * 扫描 database/migrations 下的迁移文件,按文件名顺序执行未应用的迁移,
 * 已应用记录写入 tl_migration 表;支持 seeders 执行。
 * Author: qiufeng
 */
final class Migrator
{
    public function __construct(
        private readonly string $migrationPath,
        private readonly string $seederPath
    ) {
    }

    /** @return string[] 本次执行的迁移名 */
    public function migrate(): array
    {
        $this->ensureMigrationTable();

        $applied = Db::table('tl_migration')->column('name');
        $executed = [];

        foreach ($this->migrationFiles() as $name => $file) {
            if (in_array($name, $applied, true)) {
                continue;
            }

            $migration = require $file;
            ($migration['up'])();

            Db::table('tl_migration')->insert(['name' => $name, 'apply_time' => time()]);
            $executed[] = $name;
        }

        return $executed;
    }

    /** @return array{applied: string[], pending: string[]} */
    public function status(): array
    {
        $this->ensureMigrationTable();

        $applied = Db::table('tl_migration')->column('name');
        $all = array_keys($this->migrationFiles());

        return [
            'applied' => array_values(array_intersect($all, $applied)),
            'pending' => array_values(array_diff($all, $applied)),
        ];
    }

    /** @return string[] 本次执行的种子名 */
    public function seed(): array
    {
        $executed = [];

        foreach (glob($this->seederPath . '/*.php') ?: [] as $file) {
            $seeder = require $file;
            if (($seeder['run'])() !== false) {
                $executed[] = basename($file, '.php');
            }
        }

        return $executed;
    }

    /** @return array<string, string> name => file */
    private function migrationFiles(): array
    {
        $files = [];
        foreach (glob($this->migrationPath . '/*.php') ?: [] as $file) {
            $files[basename($file, '.php')] = $file;
        }
        ksort($files);

        return $files;
    }

    private function ensureMigrationTable(): void
    {
        Db::execute(<<<SQL
            CREATE TABLE IF NOT EXISTS `tl_migration` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(191) NOT NULL,
                `apply_time` int unsigned NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL);
    }
}
