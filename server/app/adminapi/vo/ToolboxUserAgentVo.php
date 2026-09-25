<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · User-Agent 解析结果。
 * Author: qiufeng
 */
final class ToolboxUserAgentVo extends BaseVo
{
    #[ApiField('浏览器', example: 'Chrome')]
    public string $browser;

    #[ApiField('浏览器版本', example: '140.0')]
    public string $browser_version;

    #[ApiField('操作系统', example: 'Windows')]
    public string $os;

    #[ApiField('系统版本', example: '10/11')]
    public string $os_version;

    #[ApiField('设备类型:desktop/mobile/tablet/robot/unknown', example: 'desktop')]
    public string $device_type;

    #[ApiField('是否爬虫', example: false)]
    public bool $is_robot;

    #[ApiField('原始 UA', example: 'Mozilla/5.0 ...')]
    public string $user_agent;
}
