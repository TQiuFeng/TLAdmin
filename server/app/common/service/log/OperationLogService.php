<?php

namespace app\common\service\log;

use app\common\database\Mongo;
use app\common\http\Request;
use app\common\http\Response;
use MongoDB\BSON\Regex;
use MongoDB\Driver\WriteConcern;
use think\facade\Db;

/**
 * 操作日志服务(存储于 MongoDB 的 operation_log 集合)。
 *
 * 记录管理员写操作(POST/PUT/PATCH/DELETE):路由、权限标识、IP、UA、请求摘要、
 * 响应状态和耗时;密码、token、密钥等敏感字段脱敏后存储。
 * Author: qiufeng
 */
final class OperationLogService
{
    public const COLLECTION = 'operation_log';

    private const SENSITIVE_KEYS = [
        'password', 'old_password', 'new_password', 'confirm_password',
        'token', 'access_token', 'refresh_token',
        'secret', 'app_secret', 'secret_key', 'access_key', 'private_key',
    ];

    public function record(Request $request, Response $response, float $startedAt): void
    {
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return;
        }

        if ($request->authUserId() === 0) {
            return;
        }

        // 被限流的请求未执行任何操作,且高频 429 落库会让限流失去保护日志库的意义
        if ($response->status() === 429) {
            return;
        }

        try {
            $username = (string) Db::table('tl_admin_user')
                ->where('id', $request->authUserId())
                ->value('username');

            // w=0 不等待写确认,日志落库不阻塞响应
            Mongo::collection(self::COLLECTION)->insertOne([
                'user_id' => $request->authUserId(),
                'username' => $username,
                'method' => $request->method(),
                'path' => mb_substr($request->path(), 0, 191),
                'permission' => (string) $request->routeMeta('permission', ''),
                'ip' => $request->clientIp(),
                'user_agent' => mb_substr((string) $request->header('user-agent', ''), 0, 500),
                'params' => json_encode($this->maskSensitive($request->all()), JSON_UNESCAPED_UNICODE),
                'status_code' => $response->status(),
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'request_id' => (string) ($response->headers()['X-Request-Id'] ?? ''),
                'create_time' => time(),
            ], ['writeConcern' => new WriteConcern(0)]);
        } catch (\Throwable) {
            // 日志写入失败不影响业务响应
        }
    }

    /** @return array{list: array, pagination: array} */
    public function paginate(array $filters, int $page, int $pageSize): array
    {
        $filter = [];

        if (($filters['username'] ?? '') !== '') {
            $filter['username'] = new Regex(preg_quote($filters['username'], '/'), 'i');
        }
        if (($filters['path'] ?? '') !== '') {
            $filter['path'] = new Regex(preg_quote($filters['path'], '/'), 'i');
        }
        if (($filters['request_id'] ?? '') !== '') {
            // 请求 ID 精确匹配(用于按一次请求追踪)
            $filter['request_id'] = (string) $filters['request_id'];
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

    private function maskSensitive(array $params): array
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $params[$key] = $this->maskSensitive($value);
                continue;
            }

            if (in_array(strtolower((string) $key), self::SENSITIVE_KEYS, true)) {
                $params[$key] = '******';
            }
        }

        return $params;
    }
}
