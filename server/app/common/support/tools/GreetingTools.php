<?php

namespace app\common\support\tools;

use DateTimeInterface;

/**
 * 时间段问候语工具。
 *
 * 根据当前（或指定）时间返回问候语和提示语，纯本地实现。
 * Author: qiufeng
 */
trait GreetingTools
{
    /**
     * 返回时间段问候语,如「上午好」。
     *
     * @param int|string|DateTimeInterface $time     参照时间,默认现在
     * @param string                       $timezone 时区,默认 Asia/Shanghai
     * @return string 问候语
     */
    public static function greeting(int|string|DateTimeInterface $time = 'now', string $timezone = 'Asia/Shanghai'): string
    {
        return self::greetingInfo($time, $timezone)['greeting'];
    }

    /**
     * 返回时间段问候语和提示语。
     *
     * @param int|string|DateTimeInterface $time     参照时间,默认现在
     * @param string                       $timezone 时区,默认 Asia/Shanghai
     * @return array{period:string,greeting:string,tip:string,hour:int} 时段标识、问候语、提示语、小时数
     */
    public static function greetingInfo(int|string|DateTimeInterface $time = 'now', string $timezone = 'Asia/Shanghai'): array
    {
        $hour = (int) self::dateFormat(self::timestamp($time, $timezone), 'G', $timezone);

        [$period, $greeting, $tip] = match (true) {
            $hour < 5 => ['before_dawn', '凌晨好', '夜深了，注意休息，别熬坏了身体。'],
            $hour < 9 => ['morning', '早上好', '一日之计在于晨，记得吃早餐哦。'],
            $hour < 11 => ['forenoon', '上午好', '专注工作的同时，别忘了多喝水。'],
            $hour < 13 => ['noon', '中午好', '午饭时间到了，吃完小憩一会儿吧。'],
            $hour < 17 => ['afternoon', '下午好', '起来活动活动，伸个懒腰再继续。'],
            $hour < 19 => ['evening', '傍晚好', '辛苦一天了，准备下班好好放松吧。'],
            default => ['night', '晚上好', '今天辛苦了，早点休息，晚安好梦。'],
        };

        return ['period' => $period, 'greeting' => $greeting, 'tip' => $tip, 'hour' => $hour];
    }
}
