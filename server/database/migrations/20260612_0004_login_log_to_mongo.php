<?php

/**
 * 登录日志迁移到 MongoDB。
 *
 * 为 login_log 集合建索引(username、create_time),
 * 并把 tl_login_log 表的存量数据搬到 Mongo 后删表。
 * Author: qiufeng
 */

use app\common\database\Mongo;
use app\common\service\log\LoginLogService;
use think\facade\Db;

return [
    'up' => static function (): void {
        $collection = Mongo::collection(LoginLogService::COLLECTION);

        $collection->createIndexes([
            ['key' => ['username' => 1]],
            ['key' => ['create_time' => 1]],
        ]);

        $lastId = 0;
        while (true) {
            $rows = Db::table('tl_login_log')
                ->where('id', '>', $lastId)
                ->order('id')
                ->limit(500)
                ->select()
                ->toArray();

            if ($rows === []) {
                break;
            }

            $lastId = (int) end($rows)['id'];
            $collection->insertMany(array_map(static function (array $row): array {
                unset($row['id']);

                return $row;
            }, $rows));
        }

        Db::execute('DROP TABLE IF EXISTS `tl_login_log`');
    },
];
