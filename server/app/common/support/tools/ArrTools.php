<?php

namespace app\common\support\tools;

/**
 * 数组工具。
 *
 * 提供点号取值、字段提取、分组、树形转换、主键化和排序，供菜单、部门、字典等模块复用。
 * 键名传空字符串时不抛异常：返回 null 并在命令行输出提示，避免线上因笔误直接报错。
 * Author: qiufeng
 */
trait ArrTools
{
    /**
     * 按键名取值,支持点号路径(如 'user.name')。
     *
     * @param array           $array   源数组
     * @param string|int|null $key     键名或点号路径;传 null 返回整个数组
     * @param mixed           $default 取不到时的默认值
     * @return mixed 命中的值;键为空字符串时返回 null 并在命令行提示
     */
    public static function arrGet(array $array, string|int|null $key, mixed $default = null): mixed
    {
        if ($key === null) {
            return $array;
        }

        if ($key === '') {
            self::arrEmptyKeyNotice(__FUNCTION__);
            return null;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        $value = $array;
        foreach (explode('.', (string) $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * 提取每行的某字段值组成一维数组。
     *
     * @param array  $array 二维数组
     * @param string $field 字段名,支持点号路径
     * @return array 字段值列表(重置索引)
     */
    public static function pluck(array $array, string $field): array
    {
        if ($field === '') {
            self::arrEmptyKeyNotice(__FUNCTION__);
            return [];
        }

        return array_values(array_map(static fn (array $item): mixed => self::arrGet($item, $field), $array));
    }

    /**
     * 按某字段值对行分组。
     *
     * @param array  $array 二维数组
     * @param string $field 分组字段,支持点号路径
     * @return array [字段值 => 该值对应的行列表]
     */
    public static function groupBy(array $array, string $field): array
    {
        if ($field === '') {
            self::arrEmptyKeyNotice(__FUNCTION__);
            return [];
        }

        $grouped = [];
        foreach ($array as $item) {
            $grouped[(string) self::arrGet($item, $field, '')][] = $item;
        }

        return $grouped;
    }

    /**
     * 扁平列表按父子关系转树形结构。
     *
     * @param array  $items    扁平节点列表
     * @param string $id       主键字段名,默认 id
     * @param string $parent   父级字段名,默认 parent_id
     * @param string $children 子节点写入的字段名,默认 children
     * @return array 树(顶层为 parent 指向不存在节点的项)
     */
    public static function tree(array $items, string $id = 'id', string $parent = 'parent_id', string $children = 'children'): array
    {
        $map = [];
        foreach ($items as $item) {
            $item[$children] = [];
            $map[$item[$id]] = $item;
        }

        $tree = [];
        foreach ($map as $key => &$item) {
            $parentId = $item[$parent] ?? 0;
            if ($parentId && isset($map[$parentId])) {
                $map[$parentId][$children][] = &$item;
            } else {
                $tree[] = &$item;
            }
        }

        return $tree;
    }

    /**
     * 一维/二维数组按某字段变为主键索引。
     *
     * 二维数组取每行的 $field 值作为键;一维数组直接用元素值作为键。
     *
     * @param array           $array 源数组
     * @param string|int|null $field 作为键的字段名(二维)或 null(一维用元素值)
     * @return array|null 主键化后的数组;字段为空字符串时返回 null 并在命令行提示
     */
    public static function keyBy(array $array, string|int|null $field = null): ?array
    {
        if ($field === '') {
            self::arrEmptyKeyNotice(__FUNCTION__);
            return null;
        }

        $result = [];
        foreach ($array as $item) {
            if (is_array($item)) {
                $key = self::arrGet($item, $field);
                if ($key === null) {
                    continue;
                }
                $result[(string) $key] = $item;
            } else {
                $result[(string) $item] = $item;
            }
        }

        return $result;
    }

    /**
     * 按字段/值排序,一维、二维数组通用。
     *
     * @param array           $array        源数组
     * @param string|int|null $field        排序字段(二维)或 null(一维按值)
     * @param string          $order        排序方向 asc/desc,默认 asc
     * @param bool            $preserveKeys 是否保留原键,默认 false
     * @return array|null 排序后的数组;字段为空字符串时返回 null 并在命令行提示
     */
    public static function sortBy(array $array, string|int|null $field = null, string $order = 'asc', bool $preserveKeys = false): ?array
    {
        if ($field === '') {
            self::arrEmptyKeyNotice(__FUNCTION__);
            return null;
        }

        $desc = strtolower($order) === 'desc';

        $compare = static function (mixed $a, mixed $b) use ($field, $desc): int {
            $left = $field === null ? $a : (is_array($a) ? self::arrGet($a, $field) : $a);
            $right = $field === null ? $b : (is_array($b) ? self::arrGet($b, $field) : $b);

            $result = $left <=> $right;

            return $desc ? -$result : $result;
        };

        if ($preserveKeys) {
            uasort($array, $compare);
        } else {
            usort($array, $compare);
        }

        return $array;
    }

    private static function arrEmptyKeyNotice(string $method): void
    {
        if (PHP_SAPI === 'cli') {
            fwrite(STDERR, "[Tools] {$method}() 的键名参数为空字符串，已返回 null，请检查调用代码。" . PHP_EOL);
        }
    }
}
