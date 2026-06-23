<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 手机号归属地查询结果。
 * Author: qiufeng
 */
final class PhoneLocationVo extends BaseVo
{
    #[ApiField('是否命中本地号段库', example: true)]
    public bool $found;

    #[ApiField('查询的手机号', example: '13800138000')]
    public string $phone;

    #[ApiField('命中的号段前缀,命中时返回', example: '1380013')]
    public string $prefix;

    #[ApiField('省份,命中时返回', example: '北京')]
    public string $province;

    #[ApiField('城市,命中时返回', example: '北京')]
    public string $city;

    #[ApiField('运营商,命中时返回', example: '中国移动')]
    public string $operator;

    #[ApiField('区号,命中时返回', example: '010')]
    public string $area_code;

    #[ApiField('邮编,命中时返回', example: '100000')]
    public string $postcode;

    #[ApiField('未命中或格式错误时的说明')]
    public string $message;
}
