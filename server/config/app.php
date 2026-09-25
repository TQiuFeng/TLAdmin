<?php

return [
    'app' => [
        'name' => \app\common\config\EnvLoader::get('APP_NAME') ?: 'TLAdmin',
        'env' => \app\common\config\EnvLoader::get('APP_ENV') ?: 'local',
        'debug' => filter_var(\app\common\config\EnvLoader::get('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
        'key' => \app\common\config\EnvLoader::get('APP_KEY') ?: '',
    ],
];
