<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 万年历 · 按月。
 * Author: qiufeng
 */
final class ToolboxCalendarVo extends BaseVo
{
    #[ApiField('月份', example: '2026-10')]
    public string $month;

    #[ApiField('今天')]
    public ToolboxCalendarDayVo $today;

    #[ApiField('下一个节假日')]
    public ?ToolboxNextHolidayVo $next_holiday;

    #[ApiField('当月每天', listOf: ToolboxCalendarDayVo::class)]
    public array $days;
}
