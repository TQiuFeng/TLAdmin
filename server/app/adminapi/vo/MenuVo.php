<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 菜单/权限节点(树形)。
 * Author: qiufeng
 */
final class MenuVo extends BaseVo
{
    #[ApiField('菜单 ID', example: 1)]
    public int $id;

    #[ApiField('父级 ID,0 表示顶级')]
    public int $parent_id;

    #[ApiField('节点类型,catalog/menu/button/api', example: 'menu')]
    public string $type;

    #[ApiField('显示名称')]
    public string $title;

    #[ApiField('前端路由名')]
    public string $name;

    #[ApiField('前端路由路径')]
    public string $path;

    #[ApiField('前端组件路径')]
    public string $component;

    #[ApiField('图标')]
    public string $icon;

    #[ApiField('权限标识', example: 'system:user:list')]
    public string $permission;

    #[ApiField('排序值')]
    public int $sort;

    #[ApiField('菜单是否显示,1 显示 0 隐藏', example: 1)]
    public int $visible;

    #[ApiField('状态,1 启用 0 禁用', example: 1)]
    public int $status;

    #[ApiField('创建时间戳')]
    public int $create_time;

    #[ApiField('更新时间戳')]
    public int $update_time;

    #[ApiField('子节点列表,结构同当前节点', listOf: MenuVo::class)]
    public array $children;
}
