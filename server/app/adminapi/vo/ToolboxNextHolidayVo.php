<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 万年历 · 下一个节假日。
 * Author: qiufeng
 */
final class ToolboxNextHolidayVo extends BaseVo
{
    #[ApiField('节假日名称', example: '国庆节')]
    public string $name;

    #[ApiField('放假日期', example: '2026-10-01')]
    public string $date;

    #[ApiField('对应的节日当天', example: '2026-10-01')]
    public string $target;

    #[ApiField('距今天数,0 表示就是今天', example: 6)]
    public int $days_until;
}
