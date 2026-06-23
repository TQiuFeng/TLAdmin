<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * API 默认响应格式配置。
 * Author: qiufeng
 */
final class ApiResponseFormatVo extends BaseVo
{
    #[ApiField('配置键,固定为 api.response_format', example: 'api.response_format')]
    public string $field;

    #[ApiField('当前格式,json/xml', example: 'json')]
    public string $value;

    #[ApiField('可选格式', listOf: 'string')]
    public array $options;
}
