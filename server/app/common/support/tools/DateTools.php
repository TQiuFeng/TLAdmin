<?php

namespace app\common\support\tools;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

/**
 * 日期时间工具。
 *
 * 基于 Carbon 做日期转换、格式化和时间范围处理；未安装依赖时使用 PHP 原生 DateTime 兜底。
 * Author: qiufeng
 */
trait DateTools
{
    /**
     * 当前时间的格式化字符串。
     *
     * @param string $timezone 时区,默认 Asia/Shanghai
     * @param string $format   输出格式,同 date(),默认 Y-m-d H:i:s
     * @return string 当前时间字符串
     */
    public static function now(string $timezone = 'Asia/Shanghai', string $format = 'Y-m-d H:i:s'): string
    {
        if (class_exists(\Carbon\Carbon::class)) {
            return \Carbon\Carbon::now($timezone)->format($format);
        }

        return (new DateTimeImmutable('now', new DateTimeZone($timezone)))->format($format);
    }

    /**
     * 任意时间按指定时区格式化输出。
     *
     * @param int|string|DateTimeInterface $time     时间,接受 Unix 时间戳 / 日期字符串 / DateTime 对象
     * @param string                       $format   输出格式,同 date(),默认 Y-m-d H:i:s
     * @param string                       $timezone 时区,默认 Asia/Shanghai
     * @return string 格式化后的时间字符串
     */
    public static function dateFormat(int|string|DateTimeInterface $time, string $format = 'Y-m-d H:i:s', string $timezone = 'Asia/Shanghai'): string
    {
        if (class_exists(\Carbon\Carbon::class)) {
            return self::carbonOf($time, $timezone)->format($format);
        }

        $date = $time instanceof DateTimeInterface
            ? DateTimeImmutable::createFromInterface($time)
            : new DateTimeImmutable(is_numeric($time) ? '@' . $time : (string) $time);

        return $date->setTimezone(new DateTimeZone($timezone))->format($format);
    }

    /**
     * 时间戳输入必须用 createFromTimestamp：Carbon::parse(int, tz) 会忽略时区参数，导致输出按 UTC 格式化。
     */
    private static function carbonOf(int|string|DateTimeInterface $time, string $timezone): \Carbon\Carbon
    {
        if (is_int($time) || (is_string($time) && ctype_digit($time))) {
            return \Carbon\Carbon::createFromTimestamp((int) $time, $timezone);
        }

        return \Carbon\Carbon::parse($time, $timezone);
    }

    /**
     * 任意时间转 Unix 时间戳(秒)。
     *
     * @param int|string|DateTimeInterface $time     时间,默认 'now' 取当前时间
     * @param string                       $timezone 时区,默认 Asia/Shanghai
     * @return int Unix 时间戳(秒)
     */
    public static function timestamp(int|string|DateTimeInterface $time = 'now', string $timezone = 'Asia/Shanghai'): int
    {
        if (class_exists(\Carbon\Carbon::class)) {
            return self::carbonOf($time, $timezone)->timestamp;
        }

        if ($time instanceof DateTimeInterface) {
            return $time->getTimestamp();
        }

        return (new DateTimeImmutable((string) $time, new DateTimeZone($timezone)))->getTimestamp();
    }

    /**
     * 取某天的起始时刻 00:00:00。
     *
     * @param int|string|DateTimeInterface $time     某天内的任意时间,默认今天
     * @param string                       $timezone 时区,默认 Asia/Shanghai
     * @return string 当天 00:00:00 的时间字符串
     */
    public static function startOfDay(int|string|DateTimeInterface $time = 'now', string $timezone = 'Asia/Shanghai'): string
    {
        return self::dateFormat(self::timestamp($time, $timezone), 'Y-m-d 00:00:00', $timezone);
    }

    /**
     * 取某天的结束时刻 23:59:59。
     *
     * @param int|string|DateTimeInterface $time     某天内的任意时间,默认今天
     * @param string                       $timezone 时区,默认 Asia/Shanghai
     * @return string 当天 23:59:59 的时间字符串
     */
    public static function endOfDay(int|string|DateTimeInterface $time = 'now', string $timezone = 'Asia/Shanghai'): string
    {
        return self::dateFormat(self::timestamp($time, $timezone), 'Y-m-d 23:59:59', $timezone);
    }

    /**
     * 把起止时间各自格式化后返回区间。
     *
     * @param string $start    开始时间(日期字符串)
     * @param string $end      结束时间(日期字符串)
     * @param string $format   输出格式,默认 Y-m-d H:i:s
     * @param string $timezone 时区,默认 Asia/Shanghai
     * @return array{0:string,1:string} [开始, 结束]
     */
    public static function dateRange(string $start, string $end, string $format = 'Y-m-d H:i:s', string $timezone = 'Asia/Shanghai'): array
    {
        return [
            self::dateFormat($start, $format, $timezone),
            self::dateFormat($end, $format, $timezone),
        ];
    }

    /**
     * 今天的起止时间。
     *
     * @param string $timezone 时区,默认 Asia/Shanghai
     * @return array{0:string,1:string} [今天 00:00:00, 今天 23:59:59]
     */
    public static function todayRange(string $timezone = 'Asia/Shanghai'): array
    {
        return [self::startOfDay('now', $timezone), self::endOfDay('now', $timezone)];
    }

    /**
     * 所在月份的起止时间。
     *
     * @param int|string|DateTimeInterface $time     月份内的任意时间,默认本月
     * @param string                       $timezone 时区,默认 Asia/Shanghai
     * @return array{0:string,1:string} [月初 00:00:00, 月末 23:59:59]
     */
    public static function monthRange(int|string|DateTimeInterface $time = 'now', string $timezone = 'Asia/Shanghai'): array
    {
        if (class_exists(\Carbon\Carbon::class)) {
            $date = self::carbonOf($time, $timezone);
            return [$date->copy()->startOfMonth()->format('Y-m-d H:i:s'), $date->copy()->endOfMonth()->format('Y-m-d H:i:s')];
        }

        $date = new DateTimeImmutable(is_numeric($time) ? '@' . $time : (string) $time, new DateTimeZone($timezone));
        $date = $date->setTimezone(new DateTimeZone($timezone));

        return [
            $date->modify('first day of this month')->setTime(0, 0)->format('Y-m-d H:i:s'),
            $date->modify('last day of this month')->setTime(23, 59, 59)->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * 所在年份的起止时间。
     *
     * @param int|string|DateTimeInterface $time     年份内的任意时间,默认今年
     * @param string                       $timezone 时区,默认 Asia/Shanghai
     * @return array{0:string,1:string} [年初 00:00:00, 年末 23:59:59]
     */
    public static function yearRange(int|string|DateTimeInterface $time = 'now', string $timezone = 'Asia/Shanghai'): array
    {
        if (class_exists(\Carbon\Carbon::class)) {
            $date = self::carbonOf($time, $timezone);
            return [$date->copy()->startOfYear()->format('Y-m-d H:i:s'), $date->copy()->endOfYear()->format('Y-m-d H:i:s')];
        }

        $year = self::dateFormat($time, 'Y', $timezone);

        return ["{$year}-01-01 00:00:00", "{$year}-12-31 23:59:59"];
    }

    /**
     * 在给定时间上加减天数。
     *
     * @param int|string|DateTimeInterface $time     基准时间
     * @param int                          $days     增减的天数,负数为往前
     * @param string                       $format   输出格式,默认 Y-m-d H:i:s
     * @param string                       $timezone 时区,默认 Asia/Shanghai
     * @return string 计算后的时间字符串
     */
    public static function addDays(int|string|DateTimeInterface $time, int $days, string $format = 'Y-m-d H:i:s', string $timezone = 'Asia/Shanghai'): string
    {
        if (class_exists(\Carbon\Carbon::class)) {
            return self::carbonOf($time, $timezone)->addDays($days)->format($format);
        }

        return self::dateFormat(self::timestamp($time, $timezone) + ($days * 86400), $format, $timezone);
    }

    /**
     * 两个时间相差的天数。
     *
     * @param int|string|DateTimeInterface $start    起始时间
     * @param int|string|DateTimeInterface $end      结束时间,默认现在
     * @param string                       $timezone 时区,默认 Asia/Shanghai
     * @return int 相差天数($end - $start,end 早于 start 时为负)
     */
    public static function diffInDays(int|string|DateTimeInterface $start, int|string|DateTimeInterface $end = 'now', string $timezone = 'Asia/Shanghai'): int
    {
        if (class_exists(\Carbon\Carbon::class)) {
            return self::carbonOf($start, $timezone)->diffInDays(self::carbonOf($end, $timezone), false);
        }

        return (int) floor((self::timestamp($end, $timezone) - self::timestamp($start, $timezone)) / 86400);
    }

    /**
     * 判断时间是否落在闭区间 [start, end] 内。
     *
     * @param int|string|DateTimeInterface $time     被判断的时间
     * @param int|string|DateTimeInterface $start    区间起点
     * @param int|string|DateTimeInterface $end      区间终点
     * @param string                       $timezone 时区,默认 Asia/Shanghai
     * @return bool 在区间内返回 true(含端点)
     */
    public static function isBetween(int|string|DateTimeInterface $time, int|string|DateTimeInterface $start, int|string|DateTimeInterface $end, string $timezone = 'Asia/Shanghai'): bool
    {
        $target = self::timestamp($time, $timezone);

        return $target >= self::timestamp($start, $timezone) && $target <= self::timestamp($end, $timezone);
    }

    /**
     * 人性化相对时间,如「3 天前」「2 小时后」。
     *
     * @param int|string|DateTimeInterface $time     目标时间
     * @param int|string|DateTimeInterface $base     参照时间,默认现在
     * @param string                       $timezone 时区,默认 Asia/Shanghai
     * @return string 中文相对时间描述
     */
    public static function humanDiff(int|string|DateTimeInterface $time, int|string|DateTimeInterface $base = 'now', string $timezone = 'Asia/Shanghai'): string
    {
        if (class_exists(\Carbon\Carbon::class)) {
            return self::carbonOf($time, $timezone)
                ->locale('zh_CN')
                ->diffForHumans(self::carbonOf($base, $timezone));
        }

        $seconds = abs(self::timestamp($base, $timezone) - self::timestamp($time, $timezone));
        return match (true) {
            $seconds < 60 => $seconds . '秒',
            $seconds < 3600 => floor($seconds / 60) . '分钟',
            $seconds < 86400 => floor($seconds / 3600) . '小时',
            default => floor($seconds / 86400) . '天',
        };
    }
}
