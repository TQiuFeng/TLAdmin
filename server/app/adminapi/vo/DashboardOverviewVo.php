<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 仪表盘概览(按权限返回,没有权限的块不出现)。
 * Author: qiufeng
 */
final class DashboardOverviewVo extends BaseVo
{
    #[ApiField('统计卡片', listOf: DashboardStatVo::class)]
    public array $stats;

    #[ApiField('近 7 天操作次数,需要操作日志查看权限', listOf: DashboardTrendPointVo::class)]
    public array $operation_trend;

    #[ApiField('最近 5 次登录,需要登录日志查看权限', listOf: DashboardLoginVo::class)]
    public array $recent_logins;
}
