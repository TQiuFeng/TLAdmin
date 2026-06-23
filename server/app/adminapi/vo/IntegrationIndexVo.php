<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 第三方能力总览。
 * Author: qiufeng
 */
final class IntegrationIndexVo extends BaseVo
{
    #[ApiField('各分组配置详情', listOf: IntegrationDetailVo::class)]
    public array $list;

    #[ApiField('各服务状态')]
    public IntegrationServicesVo $services;
}
