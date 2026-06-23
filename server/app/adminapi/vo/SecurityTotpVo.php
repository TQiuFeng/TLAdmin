<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 动态验证码全局开关配置。
 * Author: qiufeng
 */
final class SecurityTotpVo extends BaseVo
{
    #[ApiField('是否开启动态验证码二次校验', example: true)]
    public bool $enabled;

    #[ApiField('验证器中显示的签发方名称', example: 'TLAdmin')]
    public string $issuer;
}
