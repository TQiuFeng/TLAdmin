<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 第三方能力服务状态。
 * Author: qiufeng
 */
final class IntegrationStatusVo extends BaseVo
{
    #[ApiField('Composer 包名', example: 'yansongda/pay')]
    public string $package;

    #[ApiField('探测类名')]
    public string $class;

    #[ApiField('是否已安装', example: true)]
    public bool $installed;

    #[ApiField('当前配置值,密钥字段已脱敏')]
    public object $config;
}
