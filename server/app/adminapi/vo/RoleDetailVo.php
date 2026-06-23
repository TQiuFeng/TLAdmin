<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 角色详情(行字段 + 已分配权限)。
 * Author: qiufeng
 */
final class RoleDetailVo extends RoleVo
{
    #[ApiField('已分配菜单 ID', listOf: 'int')]
    public array $menu_ids;

    #[ApiField('自定义数据权限部门 ID', listOf: 'int')]
    public array $dept_ids;
}
