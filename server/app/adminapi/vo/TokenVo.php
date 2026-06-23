<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 登录/刷新 token 返回。
 * Author: qiufeng
 */
final class TokenVo extends BaseVo
{
    #[ApiField('访问令牌,请求时放入 Authorization: Bearer 头')]
    public string $access_token;

    #[ApiField('刷新令牌,access_token 过期后用于换发新令牌')]
    public string $refresh_token;

    #[ApiField('access_token 有效期(秒)', example: 7200)]
    public int $expires_in;
}
