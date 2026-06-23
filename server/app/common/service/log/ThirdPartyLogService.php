<?php

namespace app\common\service\log;

use app\common\database\Mongo;
use MongoDB\BSON\Regex;
use MongoDB\Driver\WriteConcern;

/**
 * 第三方请求日志服务(存储于 MongoDB 的 third_party_log 集合)。
 *
 * Tools::httpRequest 每次出站请求结束后回调 record() 落库:URL、方法、状态、
 * 耗时、错误信息;不记录请求体(可能含密钥),只记录 URL 与结果摘要。
 * Author: qiufeng
 */
final class ThirdPartyLogService
{
    public const COLLECTION = 'third_party_log';

    /**
     * 记录一次出站请求(w=0 不阻塞业务)。
     *
     * @param array $result Tools::httpRequest 的返回结构
     */
    public function record(array $result): void
    {
        try {
            $url = (string) ($result['url'] ?? '');
            $host = (string) (parse_url($url, PHP_URL_HOST) ?: '');

            Mongo::collection(self::COLLECTION)->insertOne([
                'method' => (string) ($result['method'] ?? ''),
                'url' => mb_substr($url, 0, 500),
                'host' => $host,
                'ok' => (bool) ($result['ok'] ?? false),
                'status' => (int) ($result['status'] ?? 0),
                'duration_ms' => (int) ($result['duration_ms'] ?? 0),
                'error' => mb_substr((string) ($result['error'] ?? ''), 0, 500),
                'response_size' => strlen((string) ($result['body'] ?? '')),
                'create_time' => time(),
            ], ['writeConcern' => new WriteConcern(0)]);
        } catch (\Throwable) {
            // 日志写入失败不影响业务
        }
    }

    /** @return array{list: array, pagination: array} */
    public function paginate(array $filters, int $page, int $pageSize): array
    {
        $filter = [];

        if (($filters['host'] ?? '') !== '') {
            $filter['host'] = new Regex(preg_quote($filters['host'], '/'), 'i');
        }
        if (($filters['ok'] ?? '') !== '') {
            $filter['ok'] = (bool) (int) $filters['ok'];
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
