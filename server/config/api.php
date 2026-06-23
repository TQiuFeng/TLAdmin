<?php

return [
    'api' => [
        'response_format' => getenv('API_RESPONSE_FORMAT') ?: 'json',
        'response_format_options' => ['json', 'xml'],
        // 接口文档/调试台:仅 APP_DEBUG=true 时开放;auth 为可选的 Basic Auth 账号密码保护,默认不开启
        'docs' => [
            'auth' => [
                'enabled' => filter_var(getenv('API_DOCS_AUTH') ?: false, FILTER_VALIDATE_BOOL),
                'username' => getenv('API_DOCS_USERNAME') ?: 'admin',
                'password' => getenv('API_DOCS_PASSWORD') ?: '',
            ],
        ],
    ],
];
