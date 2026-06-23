<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 健康检查返回。
 * Author: qiufeng
 */
final class HealthVo extends BaseVo
{
    #[ApiField('应用名称', example: 'TLAdmin')]
    public string $app;

    #[ApiField('运行环境,local/testing/production', example: 'local')]
    public string $env;

    #[ApiField('API 默认响应格式,json/xml', example: 'json')]
    public string $api_response_format;

    #[ApiField('运行状态,固定为 running', example: 'running')]
    public string $status;
}
