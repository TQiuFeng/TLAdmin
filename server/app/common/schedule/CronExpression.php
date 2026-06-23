<?php

namespace app\common\schedule;

/**
 * 极简 crontab 表达式解析:分 时 日 月 周(五段)。
 *
 * 支持 * 、数字、a-b 范围、a,b 列表、*\/n 与 a-b/n 步长;周 0 和 7 都是周日。
 * Author: qiufeng
 */
final class CronExpression
{
    /** 表达式在指定时间(默认当前分钟)是否到期 */
    public static function isDue(string $expression, ?int $timestamp = null): bool
    {
        $timestamp = $timestamp ?? time();
        $fields = preg_split('/\s+/', trim($expression));
        if (!is_array($fields) || count($fields) !== 5) {
            throw new \InvalidArgumentException("无效的 cron 表达式:{$expression}");
        }

        [$minute, $hour, $day, $month, $weekday] = $fields;

        return self::matches($minute, (int) date('i', $timestamp), 0, 59)
            && self::matches($hour, (int) date('G', $timestamp), 0, 23)
            && self::matches($day, (int) date('j', $timestamp), 1, 31)
            && self::matches($month, (int) date('n', $timestamp), 1, 12)
            && self::matches($weekday, (int) date('w', $timestamp), 0, 7);
    }

    private static function matches(string $field, int $value, int $min, int $max): bool
    {
        foreach (explode(',', $field) as $part) {
            $step = 1;
            if (str_contains($part, '/')) {
                [$part, $stepStr] = explode('/', $part, 2);
                $step = max(1, (int) $stepStr);
            }

            if ($part === '*' || $part === '') {
                $from = $min;
                $to = $max;
            } elseif (str_contains($part, '-')) {
                [$fromStr, $toStr] = explode('-', $part, 2);
                $from = (int) $fromStr;
                $to = (int) $toStr;
            } else {
                $from = $to = (int) $part;
            }

            // 周日 0/7 等价
            $candidates = range($from, $to, $step);
            if ($max === 7) {
                $candidates = array_map(static fn (int $v): int => $v === 7 ? 0 : $v, $candidates);
            }

            if (in_array($value, $candidates, true)) {
                return true;
            }
        }

        return false;
    }
}
