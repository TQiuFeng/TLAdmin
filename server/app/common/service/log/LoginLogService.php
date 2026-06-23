<?php

namespace app\common\service\log;

use app\common\database\Mongo;
use MongoDB\BSON\Regex;
use MongoDB\Driver\WriteConcern;

/**
 * 登录日志服务(存储于 MongoDB 的 login_log 集合)。
 *
 * 记录登录成功/失败、IP 归属地、设备信息;写入与查询均走 Mongo。
 * Author: qiufeng
 */
final class LoginLogService
{
    public const COLLECTION = 'login_log';

    public function record(array $log): void
    {
        try {
            // w=0 不等待写确认,日志落库不阻塞登录流程
            Mongo::collection(self::COLLECTION)->insertOne(
                $log,
                ['writeConcern' => new WriteConcern(0)]
            );
        } catch (\Throwable) {
            // 日志写入失败不影响登录
        }
    }

    /** @return array{list: array, pagination: array} */
    public function paginate(array $filters, int $page, int $pageSize): array
    {
        $filter = [];

        if (($filters['username'] ?? '') !== '') {
            $filter['username'] = new Regex(preg_quote($filters['username'], '/'), 'i');
        }
        if (($filters['status'] ?? '') !== '') {
            $filter['status'] = (int) $filters['status'];
        }
        if (($filters['ip'] ?? '') !== '') {
            $filter['ip'] = new Regex(preg_quote($filters['ip'], '/'), 'i');
        }
        if (($filters['start_time'] ?? '') !== '' && ($filters['end_time'] ?? '') !== '') {
            $filter['create_time'] = [
                '$gte' => strtotime((string) $filters['start_time']),
                '$lte' => strtotime((string) $filters['end_time']),
            ];
        }

        $collection = Mongo::collection(self::COLLECTION);
        $total = $collection->countDocuments($filter);
        $docs = $collection->find($filter, [
            'sort' => ['_id' => -1],
            'skip' => ($page - 1) * $pageSize,
            'limit' => $pageSize,
        ]);

        $list = [];
        foreach ($docs as $doc) {
            $doc['id'] = (string) $doc['_id'];
            unset($doc['_id']);
            $list[] = $doc;
        }

        return [
            'list' => $list,
            'pagination' => ['page' => $page, 'page_size' => $pageSize, 'total' => $total],
        ];
    }
}
