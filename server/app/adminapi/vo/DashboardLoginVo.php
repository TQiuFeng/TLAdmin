<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 仪表盘 · 最近登录。
 * Author: qiufeng
 */
final class DashboardLoginVo extends BaseVo
{
    #[ApiField('账号', example: 'admin')]
    public string $username;

    #[ApiField('IP', example: '127.0.0.1')]
    public string $ip;

    #[ApiField('归属地', example: '浙江 杭州')]
    public string $location;

    #[ApiField('1 成功 0 失败', example: 1)]
    public int $status;

    #[ApiField('登录时间(秒级时间戳)', example: 1790294586)]
    public int $create_time;
}
