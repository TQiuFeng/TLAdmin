<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 各第三方能力服务状态汇总。
 * Author: qiufeng
 */
final class IntegrationServicesVo extends BaseVo
{
    #[ApiField('支付')]
    public IntegrationStatusVo $pay;

    #[ApiField('微信公众号')]
    public IntegrationStatusVo $wechat;

    #[ApiField('微信小程序')]
    public IntegrationStatusVo $wechat_miniapp;

    #[ApiField('短信')]
    public IntegrationStatusVo $sms;

    #[ApiField('对象存储')]
    public IntegrationStatusVo $storage;
}
