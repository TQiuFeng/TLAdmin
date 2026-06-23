<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 动态验证码绑定状态。
 * Author: qiufeng
 */
final class TotpStatusVo extends BaseVo
{
    #[ApiField('全局开关是否开启,开启后已绑定账号登录必须输入动态码', example: true)]
    public bool $global_enabled;

    #[ApiField('当前账号是否已绑定动态验证码', example: false)]
    public bool $bound;
}
