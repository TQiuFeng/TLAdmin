<?php

namespace app\common\queue;

use app\common\cache\RedisClient;

/**
 * Redis 队列:即时任务用 List,延迟任务用 ZSet(score=到期时间戳)。
 *
 * 用法:
 *   Queue::push(SendMailJob::class, ['to' => 'a@b.com']);          // 立即
 *   Queue::push(SendMailJob::class, ['to' => 'a@b.com'], 60);      // 延迟 60 秒
 *   php bin/console queue:work                                      // 常驻消费
 * Author: qiufeng
 */
final class Queue
{
    private const KEY_READY = 'queue:ready';
    private const KEY_DELAYED = 'queue:delayed';
    private const MAX_ATTEMPTS = 3;
    private const RETRY_DELAY = 10;

    /** 入队。$jobClass 必须实现 JobInterface */
    public static function push(string $jobClass, array $payload = [], int $delaySeconds = 0): void
    {
        if (!is_subclass_of($jobClass, JobInterface::class)) {
            throw new \InvalidArgumentException("{$jobClass} 必须实现 " . JobInterface::class);
        }

        $message = json_encode([
            'job' => $jobClass,
            'payload' => $payload,
            'attempts' => 0,
            'pushed_at' => time(),
        ], JSON_UNESCAPED_UNICODE);

        $redis = RedisClient::connection();
        if ($delaySeconds > 0) {
            $redis->zAdd(self::KEY_DELAYED, time() + $delaySeconds, $message);
        } else {
            $redis->lPush(self::KEY_READY, $message);
        }
    }

    /** 待消费任务数(含延迟) */
    public static function size(): array
    {
        $redis = RedisClient::connection();

        return [
            'ready' => (int) $redis->lLen(self::KEY_READY),
            'delayed' => (int) $redis->zCard(self::KEY_DELAYED),
        ];
    }

    /**
     * 消费循环。$once=true 只处理一条(没有任务也立即返回),供测试和调试用。
     *
     * @return int 本次处理的任务数
     */
    public static function work(bool $once = false, ?callable $logger = null): int
    {
        $log = $logger ?? static function (string $line): void {
            echo '[' . date('H:i:s') . "] {$line}\n";
        };
        $redis = RedisClient::connection();
        $handled = 0;

        do {
            self::migrateDueDelayed();

            $item = $redis->brPop([self::KEY_READY], $once ? 1 : 3);
            if (!is_array($item) || count($item) < 2) {
                continue;
            }

            $message = json_decode((string) $item[1], true);
            if (!is_array($message) || !isset($message['job'])) {
                $log('丢弃无法解析的消息');
                continue;
            }

            $handled++;
            $jobClass = (string) $message['job'];
            try {
                /** @var JobInterface $job */
                $job = new $jobClass();
                $job->handle((array) ($message['payload'] ?? []));
                $log("完成 {$jobClass}");
            } catch (\Throwable $e) {
                $attempts = (int) ($message['attempts'] ?? 0) + 1;
                if ($attempts < self::MAX_ATTEMPTS) {
                    $message['attempts'] = $attempts;
                    $redis->zAdd(self::KEY_DELAYED, time() + self::RETRY_DELAY * $attempts, json_encode($message, JSON_UNESCAPED_UNICODE));
                    $log("失败 {$jobClass}(第 {$attempts} 次): {$e->getMessage()},已延迟重试");
                } else {
                    self::logFailed($message, $e);
                    $log("失败 {$jobClass}: {$e->getMessage()},超过最大重试次数,已写入失败日志");
                }
            }
        } while (!$once);

        return $handled;
    }

    /** 把到期的延迟任务搬到就绪队列 */
    private static function migrateDueDelayed(): void
    {
        $redis = RedisClient::connection();
        $due = $redis->zRangeByScore(self::KEY_DELAYED, '-inf', (string) time(), ['limit' => [0, 100]]);
        foreach ($due as $message) {
            // ZREM 返回 1 才入队,避免多个 worker 重复消费同一条
            if ($redis->zRem(self::KEY_DELAYED, $message) > 0) {
                $redis->lPush(self::KEY_READY, $message);
            }
        }
    }

    private static function logFailed(array $message, \Throwable $e): void
    {
        $dir = dirname(__DIR__, 3) . '/runtime/log';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        file_put_contents(
            $dir . '/queue-failed.log',
            date('Y-m-d H:i:s') . ' ' . json_encode($message, JSON_UNESCAPED_UNICODE) . ' error=' . $e->getMessage() . PHP_EOL,
            FILE_APPEND
        );
    }
}
