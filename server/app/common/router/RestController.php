<?php

namespace app\common\router;

/**
 * 控制器路由注解,类比 Spring 的 @RestController + @RequestMapping。
 *
 * 用法:
 *   #[RestController('/adminapi/system/users', tag: '管理员')]
 *   final class AdminUserController extends BaseController { ... }
 *
 * prefix 作为该控制器下所有 Mapping 的路径前缀;tag 是 OpenAPI 分组名。
 * Author: qiufeng
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class RestController
{
    public function __construct(
        public readonly string $prefix = '',
        public readonly string $tag = '默认'
    ) {
    }
}
