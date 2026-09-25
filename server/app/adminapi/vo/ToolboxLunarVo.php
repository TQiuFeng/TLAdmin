<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 万年历 · 农历信息。
 * Author: qiufeng
 */
final class ToolboxLunarVo extends BaseVo
{
    #[ApiField('农历年(中文数字)', example: '二〇二六')]
    public string $year;

    #[ApiField('农历月', example: '八')]
    public string $month;

    #[ApiField('农历日', example: '十五')]
    public string $day;

    #[ApiField('年干支', example: '丙午')]
    public string $gan_zhi_year;

    #[ApiField('月干支', example: '丁酉')]
    public string $gan_zhi_month;

    #[ApiField('日干支', example: '壬寅')]
    public string $gan_zhi_day;

    #[ApiField('生肖', example: '马')]
    public string $sheng_xiao;

    #[ApiField('节气,当天不是节气时为空', example: '秋分')]
    public string $jie_qi;

    #[ApiField('农历节日', listOf: 'string')]
    public array $festivals;

    #[ApiField('宜', listOf: 'string')]
    public array $yi;

    #[ApiField('忌', listOf: 'string')]
    public array $ji;
}
