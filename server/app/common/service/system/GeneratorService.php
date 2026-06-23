<?php

namespace app\common\service\system;

use app\common\generator\CrudGenerator;
use think\facade\Db;

/**
 * 代码生成器服务:列出可生成的数据库表,并按表生成 CRUD 全套代码。
 *
 * 路径在容器中注入(server 根目录与 web 目录),内部复用 CrudGenerator。
 * Author: qiufeng
 */
final class GeneratorService
{
    public function __construct(
        private readonly string $serverPath,
        private readonly string $webPath
    ) {
    }

    /**
     * 列出当前库所有 tl_ 业务表(名称、注释、行数、字段数)。
     *
     * @return array<int, array{name: string, comment: string, rows: int, columns: int}>
     */
    public function tables(): array
    {
        $rows = Db::query(
            "SELECT t.TABLE_NAME AS name, t.TABLE_COMMENT AS comment, t.TABLE_ROWS AS rows_count, "
            . "(SELECT COUNT(*) FROM information_schema.columns c WHERE c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME) AS columns "
            . "FROM information_schema.tables t "
            . "WHERE t.TABLE_SCHEMA = DATABASE() AND t.TABLE_NAME LIKE 'tl\\_%' "
            . "ORDER BY t.TABLE_NAME"
        );

        return array_map(static fn (array $row): array => [
            'name' => (string) $row['name'],
            'comment' => (string) ($row['comment'] ?? ''),
            'rows' => (int) ($row['rows_count'] ?? 0),
            'columns' => (int) ($row['columns'] ?? 0),
        ], $rows);
    }

    /**
     * 指定表的字段默认配置(供前端配置界面预填)。
     *
     * @return array<int, array>
     */
    public function columns(string $table): array
    {
        $this->assertTable($table);

        return (new CrudGenerator($this->serverPath, $this->webPath))->defaultColumns($table);
    }

    /**
     * 按字段配置预览将生成的全部文件内容(不写盘)。
     *
     * @param array<int, array> $columns 前端字段配置
     * @return array<int, array{display: string, language: string, content: string}>
     */
    public function preview(string $table, string $title, array $columns): array
    {
        $this->assertTable($table);

        $files = (new CrudGenerator($this->serverPath, $this->webPath))
            ->build($table, $title, $this->indexByName($columns));

        return array_map(static fn (array $f): array => [
            'display' => $f['display'],
            'language' => $f['language'],
            'content' => $f['content'],
        ], $files);
    }

    /**
     * 按字段配置生成并写盘。
     *
     * @param array<int, array> $columns 前端字段配置
     * @return array{files: string[], permission: string, menu_path: string}
     */
    public function generate(string $table, string $title, array $columns, bool $force): array
    {
        $this->assertTable($table);

        $generator = new CrudGenerator($this->serverPath, $this->webPath);
        $files = $generator->generate($table, $title, $force, $this->indexByName($columns));

        $kebab = str_replace('_', '-', preg_replace('/^tl_/', '', $table));

        return [
            'files' => $files,
            'permission' => "{$kebab}:list",
            'menu_path' => "/gen/{$kebab}",
        ];
    }

    private function assertTable(string $table): void
    {
        if (!str_starts_with($table, 'tl_')) {
            throw new \InvalidArgumentException('只能对 tl_ 前缀的业务表生成代码');
        }
    }

    /** 前端字段配置数组 → 按字段名索引的覆盖表 */
    private function indexByName(array $columns): array
    {
        $overrides = [];
        foreach ($columns as $col) {
            if (isset($col['name']) && $col['name'] !== '') {
                $overrides[(string) $col['name']] = $col;
            }
        }

        return $overrides;
    }
}
