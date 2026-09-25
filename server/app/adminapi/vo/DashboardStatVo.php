<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 仪表盘 · 统计卡片。
 * Author: qiufeng
 */
final class DashboardStatVo extends BaseVo
{
    #[ApiField('标识', example: 'members')]
    public string $key;

    #[ApiField('名称', example: '会员')]
    public string $label;

    #[ApiField('数值', example: 12)]
    public int $value;

    #[ApiField('补充说明', example: '今日新增 2')]
    public string $hint;
}
