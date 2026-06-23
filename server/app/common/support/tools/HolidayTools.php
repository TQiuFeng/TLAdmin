<?php

namespace app\common\support\tools;

use DateTimeInterface;

/**
 * 中国法定节假日与万年历工具。
 *
 * 基于 6tail/lunar-php，节假日数据（含调休补班）和农历算法全部本地内置，离线可用。
 * 数据覆盖 2001 年至今；新一年放假安排发布后升级 lunar-php 或用 HolidayUtil::fix 补充。
 * Author: qiufeng
 */
trait HolidayTools
{
    /**
     * 是否法定节假日休息日(不含普通周末)。
     *
     * @param int|string|DateTimeInterface $date 日期
     * @return bool 是法定节假日休息日返回 true
     */
    public static function isHoliday(int|string|DateTimeInterface $date): bool
    {
        self::assertLunarInstalled();

        $holiday = \com\nlf\calendar\util\HolidayUtil::getHoliday(self::dateFormat($date, 'Y-m-d'));

        return $holiday !== null && !$holiday->isWork();
    }

    /**
     * 是否休息日:法定节假日,或未被调休征用的周末。
     *
     * @param int|string|DateTimeInterface $date 日期
     * @return bool 是休息日返回 true
     */
    public static function isRestDay(int|string|DateTimeInterface $date): bool
    {
        self::assertLunarInstalled();

        $ymd = self::dateFormat($date, 'Y-m-d');
        $holiday = \com\nlf\calendar\util\HolidayUtil::getHoliday($ymd);

        if ($holiday !== null) {
            return !$holiday->isWork();
        }

        $week = (int) self::dateFormat($date, 'N');

        return $week >= 6;
    }

    /**
     * 是否工作日(考虑法定节假日和调休补班)。
     *
     * @param int|string|DateTimeInterface $date 日期
     * @return bool 是工作日返回 true
     */
    public static function isWorkday(int|string|DateTimeInterface $date): bool
    {
        return !self::isRestDay($date);
    }

    /**
     * 搜索最近的节假日(从给定日期起向后找,正处于假期中则返回当前假期)。
     *
     * @param int|string|DateTimeInterface $date    起始日期,默认今天
     * @param int                          $maxDays 向后搜索的最大天数,默认 400
     * @return array{name:string,date:string,target:string,days_until:int}|null 节日名、首个休息日、节日正日、距今天数;范围内无则 null
     */
    public static function nextHoliday(int|string|DateTimeInterface $date = 'now', int $maxDays = 400): ?array
    {
        self::assertLunarInstalled();

        $start = self::timestamp(self::dateFormat($date, 'Y-m-d') . ' 00:00:00');
        for ($i = 0; $i <= $maxDays; $i++) {
            $ymd = self::dateFormat($start + $i * 86400, 'Y-m-d');
            $holiday = \com\nlf\calendar\util\HolidayUtil::getHoliday($ymd);
            if ($holiday !== null && !$holiday->isWork()) {
                return [
                    'name' => $holiday->getName(),
                    'date' => $holiday->getDay(),
                    'target' => $holiday->getTarget(),
                    'days_until' => $i,
                ];
            }
        }

        return null;
    }

    /**
     * 查询时间范围内的法定节假日,按假期分组。
     *
     * @param int|string|DateTimeInterface $start 起始日期
     * @param int|string|DateTimeInterface $end   结束日期(早于 start 会自动交换)
     * @return array{holidays:array,makeup_workdays:array} 分组假期(name/target/dates)与调休补班日
     */
    public static function holidaysBetween(int|string|DateTimeInterface $start, int|string|DateTimeInterface $end): array
    {
        self::assertLunarInstalled();

        $from = self::timestamp(self::dateFormat($start, 'Y-m-d') . ' 00:00:00');
        $to = self::timestamp(self::dateFormat($end, 'Y-m-d') . ' 00:00:00');
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $holidays = [];
        $makeup = [];
        for ($ts = $from; $ts <= $to; $ts += 86400) {
            $ymd = self::dateFormat($ts, 'Y-m-d');
            $holiday = \com\nlf\calendar\util\HolidayUtil::getHoliday($ymd);
            if ($holiday === null) {
                continue;
            }
            if ($holiday->isWork()) {
                $makeup[] = ['date' => $ymd, 'name' => $holiday->getName()];
                continue;
            }
            $key = $holiday->getName() . '|' . $holiday->getTarget();
            $holidays[$key] ??= ['name' => $holiday->getName(), 'target' => $holiday->getTarget(), 'dates' => []];
            $holidays[$key]['dates'][] = $ymd;
        }

        return ['holidays' => array_values($holidays), 'makeup_workdays' => $makeup];
    }

    /**
     * 指定日期的万年历信息:公历、农历、干支、生肖、节气、节日、宜忌、节假日。
     *
     * @param int|string|DateTimeInterface $date 日期,默认今天
     * @return array 当日万年历数据(date/week/lunar/festivals/holiday/is_workday/is_rest_day)
     */
    public static function calendar(int|string|DateTimeInterface $date = 'now'): array
    {
        self::assertLunarInstalled();

        $ymd = self::dateFormat($date, 'Y-m-d');
        [$year, $month, $day] = array_map('intval', explode('-', $ymd));

        $solar = \com\nlf\calendar\Solar::fromYmd($year, $month, $day);
        $lunar = $solar->getLunar();
        $holiday = \com\nlf\calendar\util\HolidayUtil::getHoliday($ymd);

        return [
            'date' => $ymd,
            'week' => '周' . $solar->getWeekInChinese(),
            'lunar' => [
                'year' => $lunar->getYearInChinese(),
                'month' => $lunar->getMonthInChinese(),
                'day' => $lunar->getDayInChinese(),
                'gan_zhi_year' => $lunar->getYearInGanZhi(),
                'gan_zhi_month' => $lunar->getMonthInGanZhi(),
                'gan_zhi_day' => $lunar->getDayInGanZhi(),
                'sheng_xiao' => $lunar->getYearShengXiao(),
                'jie_qi' => $lunar->getJieQi(),
                'festivals' => $lunar->getFestivals(),
                'yi' => $lunar->getDayYi(),
                'ji' => $lunar->getDayJi(),
            ],
            'festivals' => $solar->getFestivals(),
            'holiday' => $holiday === null ? null : [
                'name' => $holiday->getName(),
                'target' => $holiday->getTarget(),
                'is_rest' => !$holiday->isWork(),
            ],
            'is_workday' => self::isWorkday($ymd),
            'is_rest_day' => self::isRestDay($ymd),
        ];
    }

    /**
     * 按月返回万年历(每天一条 calendar() 结果)。
     *
     * @param int $year  年份
     * @param int $month 月份 1-12
     * @return array 当月每天的万年历列表
     */
    public static function calendarMonth(int $year, int $month): array
    {
        $days = (int) self::dateFormat(sprintf('%04d-%02d-01', $year, $month), 't');

        $list = [];
        for ($day = 1; $day <= $days; $day++) {
            $list[] = self::calendar(sprintf('%04d-%02d-%02d', $year, $month, $day));
        }

        return $list;
    }

    /**
     * 按年返回万年历。
     *
     * @param int $year 年份
     * @return array 键为月份 1-12,值为该月每天的万年历列表
     */
    public static function calendarYear(int $year): array
    {
        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = self::calendarMonth($year, $month);
        }

        return $months;
    }

    private static function assertLunarInstalled(): void
    {
        if (!class_exists(\com\nlf\calendar\Solar::class)) {
            throw new \RuntimeException('缺少 Composer 包：6tail/lunar-php');
        }
    }
}
