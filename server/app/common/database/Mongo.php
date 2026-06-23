<?php

namespace app\common\database;

use app\common\config\EnvLoader;
use MongoDB\Client;
use MongoDB\Collection;

/**
 * MongoDB 连接。
 *
 * 基于 mongodb/mongodb 官方库,按 .env 的 MONGO_* 配置惰性建连,
 * Web 入口和 CLI 共用;驱动内部维护连接池,进程内持有单个 Client 即可。
 * Author: qiufeng
 */
final class Mongo
{
    private static ?Client $client = null;

    public static function collection(string $name): Collection
    {
        if (self::$client === null) {
            self::$client = new Client(self::uri());
        }

        return self::$client->selectCollection(
            EnvLoader::get('MONGO_DATABASE', 'tladmin'),
            $name,
            ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']]
        );
    }

    private static function uri(): string
    {
        $host = EnvLoader::get('MONGO_HOST', '127.0.0.1');
        $port = EnvLoader::get('MONGO_PORT', '27017');
        $username = EnvLoader::get('MONGO_USERNAME', '');
        $password = EnvLoader::get('MONGO_PASSWORD', '');

        $auth = $username !== ''
            ? rawurlencode($username) . ':' . rawurlencode($password) . '@'
            : '';

        return "mongodb://{$auth}{$host}:{$port}";
    }
}
