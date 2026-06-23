<?php

namespace app\job;

use app\common\database\Mongo;
use app\common\service\log\LoginLogService;
use app\common\service\log\OperationLogService;
use app\common\service\log\ThirdPartyLogService;

/**
 * 日志保留策略:清理 90 天前的 MongoDB 日志。
 * Author: qiufeng
 */
final class LogRetentionTask
{
    private const RETENTION_DAYS = 90;

    public function run(): string
    {
        $before = time() - self::RETENTION_DAYS * 86400;
        $total = 0;

        foreach ([OperationLogService::COLLECTION, LoginLogService::COLLECTION, ThirdPartyLogService::COLLECTION] as $collection) {
            $result = Mongo::collection($collection)->deleteMany(['create_time' => ['$lt' => $before]]);
            $total += $result->getDeletedCount();
        }

        return "清理 " . self::RETENTION_DAYS . " 天前日志 {$total} 条";
    }
}
