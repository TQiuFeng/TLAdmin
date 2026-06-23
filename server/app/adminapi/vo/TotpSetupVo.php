<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 动态验证码绑定凭证(扫码用)。
 * Author: qiufeng
 */
final class TotpSetupVo extends BaseVo
{
    #[ApiField('TOTP 密钥(Base32),供手动输入验证器', example: 'JBSWY3DPEHPK3PXP')]
    public string $secret;

    #[ApiField('otpauth:// 协议 URI,前端渲染为二维码供 Google Authenticator 扫描')]
    public string $otpauth_uri;

    #[ApiField('绑定会话有效期(秒),过期需重新获取', example: 600)]
    public int $expires_in;
}
