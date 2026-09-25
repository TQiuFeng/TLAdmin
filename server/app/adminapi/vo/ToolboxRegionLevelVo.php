<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 示例区划的一级选项。
 * Author: qiufeng
 */
final class ToolboxRegionLevelVo extends BaseVo
{
    #[ApiField('这一级的全部区划', listOf: ToolboxRegionVo::class)]
    public array $items;
}
