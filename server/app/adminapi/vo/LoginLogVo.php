<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 登录日志行。
 * Author: qiufeng
 */
final class LoginLogVo extends BaseVo
{
    #[ApiField('日志 ID(MongoDB ObjectId)', example: '665f1c2a3b4c5d6e7f801234')]
    public string $id;

    #[ApiField('用户 ID,登录失败时为 0')]
    public int $user_id;

    #[ApiField('登录账号')]
    public string $username;

    #[ApiField('登录 IP')]
    public string $ip;

    #[ApiField('IP 归属地')]
    public string $location;

    #[ApiField('User-Agent')]
    public string $user_agent;

    #[ApiField('结果,1 成功 0 失败', example: 1)]
    public int $status;

    #[ApiField('失败原因,成功时为空')]
    public string $message;

    #[ApiField('登录时间戳')]
    public int $create_time;
}
