<?php

namespace app\common\service\geo;

use app\common\support\Tools;

/**
 * 手机号归属地本地查询服务。
 *
 * 委托 Tools::phoneLocation，数据为全量本地号段库（约 50 万号段），不请求任何外部接口。
 * Author: qiufeng
 */
final class PhoneLocationService
{
    public function lookup(string $phone): array
    {
        return Tools::phoneLocation($phone);
    }
}
