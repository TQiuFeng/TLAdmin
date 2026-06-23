<?php

namespace app\common\support\tools;

/**
 * 邮政编码与电话区号查询工具。
 *
 * 基于本地省市区数据（support/data/geo/area.php），按地区名称模糊查询，离线可用。
 * 区县自身缺邮编/区号时向上继承所属城市的值。
 * Author: qiufeng
 */
trait PostcodeTools
{
    /**
     * 按地区名称模糊查询邮编和区号。
     *
     * @param string $name  地区名称关键词
     * @param int    $limit 最多返回条数,默认 20
     * @return array 命中地区列表,每项含 name、full_name、level(1省 2市 3区县)、zip_code、area_code
     */
    public static function postcode(string $name, int $limit = 20): array
    {
        if (trim($name) === '') {
            return [];
        }

        $keyword = trim($name);
        $index = self::chinaAreaIndex();
        $results = [];

        foreach (self::chinaAreaData() as $row) {
            if (mb_strpos($row[3], $keyword) === false && mb_strpos($row[2], $keyword) === false) {
                continue;
            }

            [$zip, $areaCode] = self::inheritZipAndCode($row, $index);

            $results[] = [
                'name' => $row[3],
                'full_name' => implode('', self::areaNameParts($row[4])),
                'level' => $row[5],
                'zip_code' => $zip,
                'area_code' => $areaCode,
            ];

            if (count($results) >= $limit) {
                break;
            }
        }

        return $results;
    }

    private static function inheritZipAndCode(array $row, array $index): array
    {
        $zip = $row[7];
        $code = $row[6];

        $current = $row;
        while (($zip === '' || $code === '') && isset($index[$current[1]])) {
            $current = $index[$current[1]];
            $zip = $zip === '' ? $current[7] : $zip;
            $code = $code === '' ? $current[6] : $code;
        }

        return [$zip, $code];
    }
}
