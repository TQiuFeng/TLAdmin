<?php

namespace app\common\queue;

/**
 * 队列任务接口:实现 handle 即可被 queue:work 消费。
 * Author: qiufeng
 */
interface JobInterface
{
    /**
     * 执行任务。抛异常视为失败,自动延迟重试(最多 3 次)。
     *
     * @param array $payload 入队时携带的数据
     */
    public function handle(array $payload): void;
}
