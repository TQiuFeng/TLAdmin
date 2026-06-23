<?php

namespace app\common\router;

/**
 * GET 路由注解,类比 Spring 的 @GetMapping。
 * Author: qiufeng
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class GetMapping extends Mapping
{
    public function httpMethod(): string
    {
        return 'GET';
    }
}
