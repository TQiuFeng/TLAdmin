<?php

return [
    'app' => [
        'name' => getenv('APP_NAME') ?: 'TLAdmin',
        'env' => getenv('APP_ENV') ?: 'local',
        'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
        'key' => getenv('APP_KEY') ?: '',
    ],
];
