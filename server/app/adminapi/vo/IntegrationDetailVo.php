<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 第三方能力配置详情。
 * Author: qiufeng
 */
final class IntegrationDetailVo extends BaseVo
{
    #[ApiField('配置分组,pay/wechat/wechat_miniapp/sms/storage', example: 'pay')]
    public string $group;

    #[ApiField('配置项定义列表,含 field/title/secret 等')]
    public array $schema;

    #[ApiField('当前配置值,密钥字段已脱敏')]
    public object $config;

    #[ApiField('依赖包状态', listOf: IntegrationPackageVo::class)]
    public array $packages;
}
