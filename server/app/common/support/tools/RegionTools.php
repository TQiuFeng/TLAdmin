<?php

namespace app\common\support\tools;

use PDO;

/**
 * 全国五级行政区划工具（省、市、区县、乡镇街道、村社区）。
 *
 * 全量约 66.5 万条数据打包在 support/data/region/region.sqlite，离线可用。
 * 编码规则：省 2 位、市 4 位、区县 6 位、乡镇街道 9 位、村社区 12 位，上级编码为前缀。
 * Author: qiufeng
 */
trait RegionTools
{
    private static ?PDO $regionDb = null;

    /**
     * 省级行政区划列表。
     *
     * @return array 省级列表,每项含 code、name、level
     */
    public static function regionProvinces(): array
    {
        return self::regionQuery('SELECT code, name, level FROM region WHERE level = 1 ORDER BY code', []);
    }

    /**
     * 下一级行政区划列表。
     *
     * @param string $code 上级区划编码;传空串返回省级
     * @return array 子级列表,每项含 code、name、level
     */
    public static function regionChildren(string $code = ''): array
    {
        if ($code === '') {
            return self::regionProvinces();
        }

        return self::regionQuery('SELECT code, name, level FROM region WHERE parent_code = ? ORDER BY code', [$code]);
    }

    /**
     * 按编码查单个区划。
     *
     * @param string $code 区划编码
     * @return array{code:string,name:string,level:int,parent_code:string}|null 不存在返回 null
     */
    public static function regionFind(string $code): ?array
    {
        $rows = self::regionQuery('SELECT code, name, level, parent_code FROM region WHERE code = ?', [$code]);

        return $rows[0] ?? null;
    }

    /**
     * 编码的完整层级路径(省→…→自身)。
     *
     * @param string $code 区划编码
     * @return array 从省到自身的节点列表,每项含 code、name、level
     */
    public static function regionPath(string $code): array
    {
        $path = [];
        $current = self::regionFind($code);
        while ($current !== null) {
            array_unshift($path, ['code' => $current['code'], 'name' => $current['name'], 'level' => $current['level']]);
            $parent = $current['parent_code'] ?? '';
            $current = $parent === '' ? null : self::regionFind($parent);
        }

        return $path;
    }

    /**
     * 编码的完整地址名称,如「北京市东城区东华门街道多福巷社区居委会」。
     *
     * @param string $code      区划编码
     * @param string $separator 各级名称之间的连接符,默认空串
     * @return string 拼接后的完整地址(已剔除「市辖区」等占位层级)
     */
    public static function regionFullName(string $code, string $separator = ''): string
    {
        $names = array_column(self::regionPath($code), 'name');
        // 直辖市/省直辖的占位层级不参与拼接
        $names = array_filter($names, static fn (string $name): bool => !in_array($name, ['市辖区', '县', '省直辖县级行政区划', '自治区直辖县级行政区划'], true));

        return implode($separator, $names);
    }

    /**
     * 按名称模糊搜索行政区划。
     *
     * @param string   $keyword 名称关键词
     * @param int|null $level   限定层级:1省 2市 3区县 4乡镇街道 5村社区;null 不限
     * @param int      $limit   最多返回条数,默认 20
     * @return array 命中列表,每项含 code、name、level、parent_code、full_name
     */
    public static function regionSearch(string $keyword, ?int $level = null, int $limit = 20): array
    {
        if ($keyword === '') {
            return [];
        }

        $sql = 'SELECT code, name, level, parent_code FROM region WHERE name LIKE ?';
        $params = ['%' . $keyword . '%'];
        if ($level !== null) {
            $sql .= ' AND level = ?';
            $params[] = $level;
        }
        $sql .= ' ORDER BY level, code LIMIT ' . max(1, $limit);

        $rows = self::regionQuery($sql, $params);
        foreach ($rows as &$row) {
            $row['full_name'] = self::regionFullName($row['code']);
        }

        return $rows;
    }

    /**
     * 行政区划树。4、5 级数据量大,建议按需用 regionChildren 懒加载。
     *
     * @param int    $maxLevel   构建到的最深层级 1-5,默认 3(到区县)
     * @param string $parentCode 起始上级编码;空串从省级开始
     * @return array 嵌套树,子节点放在 children 字段
     */
    public static function regionTree(int $maxLevel = 3, string $parentCode = ''): array
    {
        $maxLevel = max(1, min(5, $maxLevel));
        $children = self::regionChildren($parentCode);
        if ($children === []) {
            return [];
        }
        if (($children[0]['level'] ?? 1) >= $maxLevel) {
            return $children;
        }

        foreach ($children as &$child) {
            $sub = self::regionTree($maxLevel, $child['code']);
            if ($sub !== []) {
                $child['children'] = $sub;
            }
        }

        return $children;
    }

    private static function regionQuery(string $sql, array $params): array
    {
        if (self::$regionDb === null) {
            $path = __DIR__ . '/../data/region/region.sqlite';
            if (!is_file($path)) {
                throw new \RuntimeException("行政区划数据库不存在：{$path}");
            }
            self::$regionDb = new PDO('sqlite:' . $path, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        $statement = self::$regionDb->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }
}
