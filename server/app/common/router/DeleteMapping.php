<?php

namespace app\common\router;

/**
 * DELETE 路由注解,类比 Spring 的 @DeleteMapping。
 * Author: qiufeng
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class DeleteMapping extends Mapping
{
    public function httpMethod(): string
    {
        return 'DELETE';
    }
}
