<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 命中的 IP 网段。
 * Author: qiufeng
 */
final class IpRangeVo extends BaseVo
{
    #[ApiField('网段起始 IP', example: '1.0.1.0')]
    public string $start_ip;

    #[ApiField('网段结束 IP', example: '1.0.3.255')]
    public string $end_ip;
}
