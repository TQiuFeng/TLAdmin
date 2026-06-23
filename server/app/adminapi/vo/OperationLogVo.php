<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 操作日志行。
 * Author: qiufeng
 */
final class OperationLogVo extends BaseVo
{
    #[ApiField('日志 ID(MongoDB ObjectId)', example: '665f1c2a3b4c5d6e7f801234')]
    public string $id;

    #[ApiField('操作人 ID')]
    public int $user_id;

    #[ApiField('操作人账号')]
    public string $username;

    #[ApiField('请求方法', example: 'POST')]
    public string $method;

    #[ApiField('请求路径', example: '/adminapi/system/users')]
    public string $path;

    #[ApiField('接口权限标识')]
    public string $permission;

    #[ApiField('操作 IP')]
    public string $ip;

    #[ApiField('User-Agent')]
    public string $user_agent;

    #[ApiField('请求参数摘要 JSON,敏感字段已脱敏')]
    public string $params;

    #[ApiField('响应 HTTP 状态码', example: 200)]
    public int $status_code;

    #[ApiField('请求耗时(毫秒)')]
    public int $duration_ms;

    #[ApiField('请求追踪 ID')]
    public string $request_id;

    #[ApiField('操作时间戳')]
    public int $create_time;
}
