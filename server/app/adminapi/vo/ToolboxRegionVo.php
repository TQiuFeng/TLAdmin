<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 行政区划节点。
 * Author: qiufeng
 */
final class ToolboxRegionVo extends BaseVo
{
    #[ApiField('区划编码', example: '330106')]
    public string $code;

    #[ApiField('名称', example: '西湖区')]
    public string $name;

    #[ApiField('层级:1 省 2 市 3 区县 4 乡镇 5 村', example: 3)]
    public int $level;

    #[ApiField('上级编码,仅详情返回', example: '3301')]
    public string $parent_code;

    #[ApiField('是否没有下级,仅列表返回', example: false)]
    public bool $leaf;
}
