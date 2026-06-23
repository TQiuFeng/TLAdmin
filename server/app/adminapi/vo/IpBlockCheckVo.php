<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * IP 屏蔽检查结果。
 * Author: qiufeng
 */
final class IpBlockCheckVo extends BaseVo
{
    #[ApiField('屏蔽功能是否启用', example: true)]
    public bool $enabled;

    #[ApiField('该 IP 是否被屏蔽', example: false)]
    public bool $blocked;

    #[ApiField('检查的 IP', example: '1.0.1.1')]
    public string $ip;

    #[ApiField('当前模式,blacklist/whitelist', example: 'blacklist')]
    public string $mode;

    #[ApiField('判定原因')]
    public string $reason;

    #[ApiField('IP 归属地信息')]
    public IpLocationVo $location;

    #[ApiField('命中的规则,未命中为 null')]
    public ?object $matched_rule;
}
