<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 会员登录/注册返回的令牌。
 * Author: qiufeng
 */
final class MemberTokenVo extends BaseVo
{
    #[ApiField('访问令牌(后续请求放 Authorization: Bearer)')]
    public string $token;

    #[ApiField('有效期(秒)')]
    public int $expires_in;

    #[ApiField('会员 ID')]
    public int $user_id;

    #[ApiField('是否本次新注册(仅小程序登录返回)', example: false)]
    public bool $is_new;
}
