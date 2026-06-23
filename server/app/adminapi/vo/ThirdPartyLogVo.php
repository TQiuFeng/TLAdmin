<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 第三方请求日志行。
 * Author: qiufeng
 */
final class ThirdPartyLogVo extends BaseVo
{
    #[ApiField('日志 ID(MongoDB ObjectId)', example: '665f1c2a3b4c5d6e7f801234')]
    public string $id;

    #[ApiField('请求方法', example: 'POST')]
    public string $method;

    #[ApiField('请求 URL')]
    public string $url;

    #[ApiField('目标主机', example: 'oss-cn-hangzhou.aliyuncs.com')]
    public string $host;

    #[ApiField('是否成功(2xx/3xx)')]
    public bool $ok;

    #[ApiField('HTTP 状态码,0 表示连接失败', example: 200)]
    public int $status;

    #[ApiField('耗时(毫秒)')]
    public int $duration_ms;

    #[ApiField('错误信息,成功时为空')]
    public string $error;

    #[ApiField('响应体大小(字节)')]
    public int $response_size;

    #[ApiField('请求时间戳')]
    public int $create_time;
}
