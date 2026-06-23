<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * IP 归属地屏蔽配置。
 * Author: qiufeng
 */
final class IpBlockConfigVo extends BaseVo
{
    #[ApiField('是否启用 IP 屏蔽', example: false)]
    public bool $enabled;

    #[ApiField('模式,blacklist 黑名单 / whitelist 白名单', example: 'blacklist')]
    public string $mode;

    #[ApiField('是否信任代理转发头解析客户端 IP', example: false)]
    public bool $trust_proxy_headers;

    #[ApiField('不参与屏蔽检查的路径', listOf: 'string')]
    public array $excluded_paths;

    #[ApiField('屏蔽规则列表', listOf: IpBlockRuleVo::class)]
    public array $rules;
}
