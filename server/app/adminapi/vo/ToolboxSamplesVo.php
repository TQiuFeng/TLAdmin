<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 工具箱页面初始示例。
 * Author: qiufeng
 */
final class ToolboxSamplesVo extends BaseVo
{
    #[ApiField('文字处理示例')]
    public ToolboxTextVo $text;

    #[ApiField('金额示例')]
    public ToolboxMoneyVo $money;

    #[ApiField('二维码示例')]
    public ToolboxQrcodeVo $qrcode;

    #[ApiField('当前请求的 User-Agent')]
    public ToolboxUserAgentVo $user_agent;

    #[ApiField('行政区划示例')]
    public ToolboxRegionSampleVo $region;
}
