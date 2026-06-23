<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * IP 归属地查询结果。
 * Author: qiufeng
 */
final class IpLocationVo extends BaseVo
{
    #[ApiField('是否命中本地 IP 库', example: true)]
    public bool $found;

    #[ApiField('查询的 IP', example: '1.0.1.1')]
    public string $ip;

    #[ApiField('国家,命中时返回', example: '中国')]
    public string $country;

    #[ApiField('省份,命中时返回', example: '福建')]
    public string $province;

    #[ApiField('城市,命中时返回', example: '福州')]
    public string $city;

    #[ApiField('运营商,命中时返回', example: '电信')]
    public string $isp;

    #[ApiField('命中的网段,命中时返回')]
    public IpRangeVo $range;

    #[ApiField('未命中或格式错误时的说明')]
    public string $message;
}
