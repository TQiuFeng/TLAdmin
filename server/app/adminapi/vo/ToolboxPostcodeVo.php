<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 邮编与区号。
 * Author: qiufeng
 */
final class ToolboxPostcodeVo extends BaseVo
{
    #[ApiField('地区名称', example: '西湖区')]
    public string $name;

    #[ApiField('完整名称', example: '浙江省杭州市西湖区')]
    public string $full_name;

    #[ApiField('层级', example: 3)]
    public int $level;

    #[ApiField('邮编', example: '310013')]
    public string $zip_code;

    #[ApiField('电话区号', example: '0571')]
    public string $area_code;
}
