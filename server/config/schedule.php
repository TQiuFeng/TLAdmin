<?php

/**
 * 定时任务表。
 *
 * cron 五段式:分 时 日 月 周。新增任务:实现 run(): string 的类 + 在这里登记一行。
 * Author: qiufeng
 */

return [
    [
        'name' => 'log-retention',
        'cron' => '30 3 * * *',
        'handler' => \app\job\LogRetentionTask::class,
        'description' => '每天 03:30 清理 90 天前的操作/登录/第三方日志(MongoDB)',
    ],
];
