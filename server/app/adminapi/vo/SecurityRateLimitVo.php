<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 接口限流配置。
 * Author: qiufeng
 */
final class SecurityRateLimitVo extends BaseVo
{
    #[ApiField('每秒最多请求次数,0 表示不限流', example: 10)]
    public int $per_second;
}
