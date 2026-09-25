<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 仪表盘 · 操作趋势的一天。
 * Author: qiufeng
 */
final class DashboardTrendPointVo extends BaseVo
{
    #[ApiField('日期', example: '09-25')]
    public string $date;

    #[ApiField('操作次数', example: 36)]
    public int $count;
}
