<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * IP 屏蔽规则。
 * Author: qiufeng
 */
final class IpBlockRuleVo extends BaseVo
{
    #[ApiField('规则 ID', example: 'rule_1')]
    public string $id;

    #[ApiField('规则名称')]
    public string $name;

    #[ApiField('规则是否启用', example: true)]
    public bool $enabled;

    #[ApiField('匹配国家,空表示不限')]
    public string $country;

    #[ApiField('匹配省份,空表示不限')]
    public string $province;

    #[ApiField('匹配城市,空表示不限')]
    public string $city;

    #[ApiField('匹配运营商,空表示不限')]
    public string $isp;

    #[ApiField('匹配单个 IP,空表示不限')]
    public string $ip;

    #[ApiField('匹配 CIDR 网段,空表示不限', example: '10.0.0.0/8')]
    public string $cidr;

    #[ApiField('匹配 IP 区间起始,空表示不限')]
    public string $start_ip;

    #[ApiField('匹配 IP 区间结束,空表示不限')]
    public string $end_ip;

    #[ApiField('备注')]
    public string $remark;
}
