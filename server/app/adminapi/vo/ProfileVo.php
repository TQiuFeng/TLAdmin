<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 当前用户档案:基础信息 + 角色 + 权限 + 菜单树。
 * Author: qiufeng
 */
final class ProfileVo extends BaseVo
{
    #[ApiField('用户基础信息')]
    public ProfileUserVo $user;

    #[ApiField('拥有角色', listOf: ProfileRoleVo::class)]
    public array $roles;

    #[ApiField('权限标识列表,超级管理员含 *', listOf: 'string')]
    public array $permissions;

    #[ApiField('可见菜单树(catalog/menu 节点)', listOf: MenuVo::class)]
    public array $menus;
}
