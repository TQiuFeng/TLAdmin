<?php

namespace app\common\support\tools;

/**
 * 金额工具。
 *
 * 系统内部金额优先使用整数分，避免浮点精度问题；并提供大写中文金额双向转换。
 * Author: qiufeng
 */
trait MoneyTools
{
    private static array $cnMoneyDigits = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];

    /**
     * 元转整数分(四舍五入),如 1.5 → 150。
     *
     * @param int|float|string $yuan 金额(元)
     * @return int 金额(分)
     */
    public static function yuanToFen(int|float|string $yuan): int
    {
        return (int) round(((float) $yuan) * 100);
    }

    /**
     * 整数分转元字符串,如 150 → "1.50"。
     *
     * @param int $fen       金额(分)
     * @param int $precision 保留的小数位,默认 2
     * @return string 金额(元)字符串
     */
    public static function fenToYuan(int $fen, int $precision = 2): string
    {
        return number_format($fen / 100, $precision, '.', '');
    }

    /**
     * 整数分转带货币符号的元字符串,如 150 → "¥1.50"。
     *
     * @param int    $fen    金额(分)
     * @param string $symbol 货币符号,默认 ¥
     * @return string 带符号的金额字符串
     */
    public static function formatFen(int $fen, string $symbol = '¥'): string
    {
        return $symbol . self::fenToYuan($fen);
    }

    /**
     * 数字金额转大写中文金额,如 1234.56 → 壹仟贰佰叁拾肆元伍角陆分。
     *
     * 支持到万亿级,分以下四舍五入。
     *
     * @param int|float|string $amount 金额(元)
     * @return string 大写中文金额
     */
    public static function moneyToCn(int|float|string $amount): string
    {
        $negative = (float) $amount < 0;
        $fen = (int) round(abs((float) $amount) * 100);

        if ($fen === 0) {
            return '零元整';
        }

        $integer = intdiv($fen, 100);
        $jiao = intdiv($fen % 100, 10);
        $li = $fen % 10;

        $result = $integer > 0 ? self::cnIntegerPart($integer) . '元' : '';

        if ($jiao === 0 && $li === 0) {
            $result .= '整';
        } else {
            if ($jiao > 0) {
                $result .= self::$cnMoneyDigits[$jiao] . '角';
            } elseif ($integer > 0 && $li > 0) {
                $result .= '零';
            }
            if ($li > 0) {
                $result .= self::$cnMoneyDigits[$li] . '分';
            }
        }

        return ($negative ? '负' : '') . $result;
    }

    /**
     * 大写中文金额转数字金额,如 壹仟贰佰叁拾肆元伍角陆分 → 1234.56。
     *
     * 同时兼容小写中文数字(一二三)。
     *
     * @param string $cn 中文金额字符串
     * @return string 数字金额字符串(保留两位小数)
     */
    public static function cnToMoney(string $cn): string
    {
        $cn = str_replace(['人民币', '人民幣', '，', ',', ' '], '', trim($cn));
        $negative = str_starts_with($cn, '负') || str_starts_with($cn, '負');
        $cn = ltrim($cn, '负負');

        $digitMap = [
            '零' => 0, '壹' => 1, '贰' => 2, '叁' => 3, '肆' => 4, '伍' => 5, '陆' => 6, '柒' => 7, '捌' => 8, '玖' => 9,
            '〇' => 0, '一' => 1, '二' => 2, '三' => 3, '四' => 4, '五' => 5, '六' => 6, '七' => 7, '八' => 8, '九' => 9,
            '两' => 2,
        ];
        $unitMap = ['拾' => 10, '十' => 10, '佰' => 100, '百' => 100, '仟' => 1000, '千' => 1000];
        $sectionMap = ['万' => 10000, '萬' => 10000, '亿' => 100000000, '億' => 100000000];

        $intPart = $cn;
        $decPart = '';
        $split = false;
        foreach (['元', '圆', '圓'] as $splitter) {
            if (mb_strpos($cn, $splitter) !== false) {
                [$intPart, $decPart] = explode($splitter, $cn, 2);
                $split = true;
                break;
            }
        }

        // 没有「元」但带「角/分」的纯小数金额，如「伍角」「叁分」
        if (!$split && (mb_strpos($cn, '角') !== false || mb_strpos($cn, '分') !== false)) {
            $intPart = '';
            $decPart = $cn;
        }

        $total = 0;
        $section = 0;
        $current = 0;
        foreach (preg_split('//u', $intPart, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $char) {
            if (isset($digitMap[$char])) {
                $current = $digitMap[$char];
            } elseif (isset($unitMap[$char])) {
                $section += ($current === 0 && $unitMap[$char] === 10 ? 1 : $current) * $unitMap[$char];
                $current = 0;
            } elseif (isset($sectionMap[$char])) {
                $section += $current;
                $current = 0;
                if ($sectionMap[$char] === 100000000) {
                    $total = ($total + $section) * 100000000;
                    $section = 0;
                } else {
                    $section *= 10000;
                    $total += $section;
                    $section = 0;
                }
            }
        }
        $integer = $total + $section + $current;

        $fen = 0;
        $chars = preg_split('//u', $decPart, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        for ($i = 0; $i < count($chars); $i++) {
            if (!isset($digitMap[$chars[$i]])) {
                continue;
            }
            $next = $chars[$i + 1] ?? '';
            if ($next === '角') {
                $fen += $digitMap[$chars[$i]] * 10;
            } elseif ($next === '分') {
                $fen += $digitMap[$chars[$i]];
            }
        }

        $value = number_format($integer + $fen / 100, 2, '.', '');

        return ($negative ? '-' : '') . $value;
    }

    private static function cnIntegerPart(int $integer): string
    {
        $units = ['', '拾', '佰', '仟'];
        $sections = ['', '万', '亿', '万亿'];

        $parts = [];
        while ($integer > 0) {
            $parts[] = $integer % 10000;
            $integer = intdiv($integer, 10000);
        }

        $result = '';
        $lastIndex = null;
        for ($i = count($parts) - 1; $i >= 0; $i--) {
            $section = $parts[$i];
            if ($section === 0) {
                continue;
            }

            $text = '';
            $digits = (string) $section;
            $length = strlen($digits);
            $zeroPending = false;
            for ($j = 0; $j < $length; $j++) {
                $digit = (int) $digits[$j];
                if ($digit === 0) {
                    $zeroPending = $text !== '';
                    continue;
                }
                if ($zeroPending) {
                    $text .= '零';
                    $zeroPending = false;
                }
                $text .= self::$cnMoneyDigits[$digit] . $units[$length - $j - 1];
            }

            // 跨过了全零的节，或本节不足四位（千位为零），高低位衔接处补「零」
            if ($result !== '' && (($lastIndex - $i) > 1 || $section < 1000)) {
                $result .= '零';
            }

            $result .= $text . $sections[$i];
            $lastIndex = $i;
        }

        return $result;
    }
}
