<?php

namespace app\common\openapi;

/**
 * VO 属性的 OpenAPI 字段注解,类似 Java 的 @Schema。
 *
 * 用法:
 *   #[ApiField('管理员 ID', example: 1)]
 *   public int $id;
 *
 *   #[ApiField('拥有角色', listOf: RoleBriefVo::class)]   // 对象数组
 *   public array $roles;
 *
 *   #[ApiField('权限标识', listOf: 'string')]              // 标量数组
 *   public array $permissions;
 *
 * 属性类型为其他 VO 类时自动生成嵌套对象,支持递归(如菜单树 children)。
 * Author: qiufeng
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class ApiField
{
    public function __construct(
        public readonly string $description = '',
        public readonly mixed $example = null,
        public readonly ?string $listOf = null
    ) {
    }
}
