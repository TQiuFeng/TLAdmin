<?php

namespace app\common\schedule;

use app\common\cache\RedisClient;

/**
 * 定时任务调度器:读取 config/schedule.php 的任务表,按 cron 表达式触发。
 *
 * 部署:系统 crontab 每分钟执行一次
 *   * * * * * cd /path/to/server && php bin/console schedule:run >> runtime/log/schedule.log 2>&1
 * 同一任务同一分钟只触发一次(Redis 去重,多机部署也不会重复跑)。
 * Author: qiufeng
 */
final class Scheduler
{
    /** @param array $tasks config/schedule.php 的任务列表 */
    public function __construct(private readonly array $tasks)
    {
    }

    /**
     * 执行当前分钟到期的任务。
     *
     * @return array<string, string> 任务名 => 执行结果(ok / skipped / 错误信息)
     */
    public function run(): array
    {
        $results = [];
        $minute = date('Y-m-d H:i');

        foreach ($this->tasks as $task) {
            $name = (string) ($task['name'] ?? '');
            $cron = (string) ($task['cron'] ?? '');
            $handler = (string) ($task['handler'] ?? '');

            if ($name === '' || $cron === '' || !class_exists($handler)) {
                $results[$name ?: '(未命名)'] = '配置无效';
                continue;
            }
            if (!CronExpression::isDue($cron)) {
                continue;
            }

            // 同一分钟去重(SET NX,90 秒过期)
            $lockKey = "schedule:ran:{$name}:{$minute}";
            if (!RedisClient::connection()->set($lockKey, '1', ['nx', 'ex' => 90])) {
                $results[$name] = 'skipped(本分钟已执行)';
                continue;
            }

            try {
                $instance = new $handler();
                $output = $instance->run();
                $results[$name] = is_string($output) && $output !== '' ? $output : 'ok';
            } catch (\Throwable $e) {
                $results[$name] = '失败: ' . $e->getMessage();
            }
        }

        return $results;
    }
}
