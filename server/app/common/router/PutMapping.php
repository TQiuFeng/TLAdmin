<?php

namespace app\common\router;

/**
 * PUT 路由注解,类比 Spring 的 @PutMapping。
 * Author: qiufeng
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class PutMapping extends Mapping
{
    public function httpMethod(): string
    {
        return 'PUT';
    }
}
