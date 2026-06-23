<?php

/**
 * 安全配置。
 *
 * Author: qiufeng
 */
return [
    'security' => [
        // 接口限流:按登录用户(未登录按 IP)每秒最多请求次数,0 表示不限流
        'rate_limit' => [
            'per_second' => 10,
        ],
        // Google Authenticator 动态验证码:开启后已绑定账号登录必须输入 6 位动态码
        'totp' => [
            'enabled' => false,
            'issuer' => 'TLAdmin',
        ],
        'ip_block' => [
            'enabled' => false,
            'mode' => 'blacklist',
            'trust_proxy_headers' => false,
            'excluded_paths' => [
                '/adminapi/health',
                '/adminapi/openapi.json',
                '/adminapi/security/ip-block/*',
            ],
            'rules' => [],
        ],
    ],
];
