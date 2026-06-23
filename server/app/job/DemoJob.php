<?php

namespace app\job;

use app\common\queue\JobInterface;

/**
 * 示例队列任务:把 payload 写入 runtime/log/demo-job.log,演示 Queue::push 用法。
 * Author: qiufeng
 */
final class DemoJob implements JobInterface
{
    public function handle(array $payload): void
    {
        $dir = dirname(__DIR__, 2) . '/runtime/log';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        file_put_contents(
            $dir . '/demo-job.log',
            date('Y-m-d H:i:s') . ' ' . json_encode($payload, JSON_UNESCAPED_UNICODE) . PHP_EOL,
            FILE_APPEND
        );
    }
}
