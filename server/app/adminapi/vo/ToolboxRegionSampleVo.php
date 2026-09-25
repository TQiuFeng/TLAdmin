<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 示例区划:预选路径 + 各级选项 + 详情。
 * Author: qiufeng
 */
final class ToolboxRegionSampleVo extends BaseVo
{
    #[ApiField('预选的区划编码路径(省 → 市 → 区县)', listOf: 'string')]
    public array $selected;

    #[ApiField('各级选项,第 i 级是 selected[i-1] 的下级', listOf: ToolboxRegionLevelVo::class)]
    public array $levels;

    #[ApiField('最后一级的详情')]
    public ToolboxRegionDetailVo $detail;
}
