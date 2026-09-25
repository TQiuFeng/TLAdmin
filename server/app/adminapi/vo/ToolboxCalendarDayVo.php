<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 万年历 · 单日。
 * Author: qiufeng
 */
final class ToolboxCalendarDayVo extends BaseVo
{
    #[ApiField('公历日期', example: '2026-09-25')]
    public string $date;

    #[ApiField('星期', example: '周五')]
    public string $week;

    #[ApiField('农历信息')]
    public ToolboxLunarVo $lunar;

    #[ApiField('公历节日', listOf: 'string')]
    public array $festivals;

    #[ApiField('法定节假日安排,没有时为 null')]
    public ?ToolboxHolidayVo $holiday;

    #[ApiField('是否工作日(含调休上班)', example: false)]
    public bool $is_workday;

    #[ApiField('是否休息日(含周末与法定假日)', example: true)]
    public bool $is_rest_day;
}
