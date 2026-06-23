<?php

namespace app\common\database;

use app\common\config\EnvLoader;
use think\facade\Db;

/**
 * 数据库引导。
 *
 * 基于 think-orm 独立模式初始化 Db 连接,Web 入口和 CLI 共用同一份配置。
 * Author: qiufeng
 */
final class DbBootstrap
{
    private static bool $booted = false;

    public static function init(): void
    {
        if (self::$booted) {
            return;
        }

        Db::setConfig([
            'default' => 'mysql',
            'connections' => [
                'mysql' => [
                    'type' => 'mysql',
                    'hostname' => EnvLoader::get('DB_HOST', '127.0.0.1'),
                    'hostport' => EnvLoader::get('DB_PORT', '3306'),
                    'database' => EnvLoader::get('DB_DATABASE', 'tladmin'),
                    'username' => EnvLoader::get('DB_USERNAME', 'root'),
                    'password' => EnvLoader::get('DB_PASSWORD', ''),
                    'charset' => 'utf8mb4',
                    'prefix' => EnvLoader::get('DB_PREFIX', 'tl_'),
                    'fields_strict' => true,
                    'break_reconnect' => true,
                ],
            ],
        ]);

        self::$booted = true;
    }
}
