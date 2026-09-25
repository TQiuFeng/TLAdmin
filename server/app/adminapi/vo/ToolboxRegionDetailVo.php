<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 行政区划详情。
 * Author: qiufeng
 */
final class ToolboxRegionDetailVo extends BaseVo
{
    #[ApiField('区划本身')]
    public ToolboxRegionVo $region;

    #[ApiField('从省到自身的层级路径', listOf: ToolboxRegionVo::class)]
    public array $path;

    #[ApiField('完整名称', example: '浙江省杭州市西湖区')]
    public string $full_name;

    #[ApiField('邮编与区号,邮编库只到区县级,匹配不到为 null')]
    public ?ToolboxPostcodeVo $postcode;
}
