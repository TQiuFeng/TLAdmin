<?php

namespace app\common\support\tools;

/**
 * 字符串工具。
 *
 * 封装驼峰/下划线转换、前后缀判断、脱敏、截断、大小写转换和文本相似度。
 * Author: qiufeng
 */
trait StrTools
{
    /**
     * 下划线/连字符转小驼峰,如 user_name → userName。
     *
     * @param string $value 原字符串
     * @return string 小驼峰字符串
     */
    public static function camel(string $value): string
    {
        if (class_exists(\Illuminate\Support\Str::class)) {
            return \Illuminate\Support\Str::camel($value);
        }

        return lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value))));
    }

    /**
     * 驼峰转下划线,如 userName → user_name。
     *
     * @param string $value 原字符串
     * @return string 下划线字符串
     */
    public static function snake(string $value): string
    {
        if (class_exists(\Illuminate\Support\Str::class)) {
            return \Illuminate\Support\Str::snake($value);
        }

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $value) ?: $value);
    }

    /**
     * 是否以指定前缀开头。
     *
     * @param string $value  被检查的字符串
     * @param string $needle 前缀
     * @return bool 命中返回 true
     */
    public static function startsWith(string $value, string $needle): bool
    {
        return str_starts_with($value, $needle);
    }

    /**
     * 是否以指定后缀结尾。
     *
     * @param string $value  被检查的字符串
     * @param string $needle 后缀
     * @return bool 命中返回 true
     */
    public static function endsWith(string $value, string $needle): bool
    {
        return str_ends_with($value, $needle);
    }

    /**
     * 字符串脱敏:保留首尾,中间遮罩。
     *
     * @param string $value 原字符串(手机号、姓名等)
     * @param int    $start 开头保留的字符数,默认 3
     * @param int    $end   结尾保留的字符数,默认 4
     * @param string $mask  遮罩字符,默认 *
     * @return string 脱敏后字符串;总长不足 start+end 时整体遮罩
     */
    public static function mask(string $value, int $start = 3, int $end = 4, string $mask = '*'): string
    {
        $length = mb_strlen($value);
        if ($length <= $start + $end) {
            return str_repeat($mask, $length);
        }

        return mb_substr($value, 0, $start)
            . str_repeat($mask, $length - $start - $end)
            . mb_substr($value, -$end);
    }

    /**
     * 超长截断并追加后缀,多字节安全。
     *
     * @param string $value  原字符串
     * @param int    $limit  最大保留字符数,默认 80
     * @param string $suffix 截断后追加的后缀,默认 ...
     * @return string 截断后的字符串;未超长则原样返回
     */
    public static function limit(string $value, int $limit = 80, string $suffix = '...'): string
    {
        return mb_strlen($value) > $limit ? mb_substr($value, 0, $limit) . $suffix : $value;
    }

    /**
     * 转大写,多字节安全。
     *
     * @param string $value 原字符串
     * @return string 大写字符串
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value);
    }

    /**
     * 转小写,多字节安全。
     *
     * @param string $value 原字符串
     * @return string 小写字符串
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value);
    }

    /**
     * 计算两个文本的相似度百分比(基于编辑距离),多字节安全,中文可用。
     *
     * @param string $a         文本 A
     * @param string $b         文本 B
     * @param int    $precision 结果保留的小数位,默认 2
     * @return float 相似度 0-100,完全相同为 100
     */
    public static function similarity(string $a, string $b, int $precision = 2): float
    {
        if ($a === $b) {
            return 100.0;
        }

        $charsA = preg_split('//u', $a, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $charsB = preg_split('//u', $b, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $lenA = count($charsA);
        $lenB = count($charsB);

        if ($lenA === 0 || $lenB === 0) {
            return 0.0;
        }

        $previous = range(0, $lenB);
        for ($i = 1; $i <= $lenA; $i++) {
            $current = [$i];
            for ($j = 1; $j <= $lenB; $j++) {
                $cost = $charsA[$i - 1] === $charsB[$j - 1] ? 0 : 1;
                $current[$j] = min($previous[$j] + 1, $current[$j - 1] + 1, $previous[$j - 1] + $cost);
            }
            $previous = $current;
        }

        $distance = $previous[$lenB];

        return round((1 - $distance / max($lenA, $lenB)) * 100, $precision);
    }
}
