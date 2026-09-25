<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 万年历 · 法定节假日安排。
 * Author: qiufeng
 */
final class ToolboxHolidayVo extends BaseVo
{
    #[ApiField('节假日名称', example: '国庆节')]
    public string $name;

    #[ApiField('对应的节日当天', example: '2026-10-01')]
    public string $target;

    #[ApiField('true 放假,false 调休上班', example: true)]
    public bool $is_rest;
}
