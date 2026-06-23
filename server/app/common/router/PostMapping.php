<?php

namespace app\common\router;

/**
 * POST 路由注解,类比 Spring 的 @PostMapping。
 * Author: qiufeng
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class PostMapping extends Mapping
{
    public function httpMethod(): string
    {
        return 'POST';
    }
}
