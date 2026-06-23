<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 当前用户档案的角色信息。
 * Author: qiufeng
 */
final class ProfileRoleVo extends BaseVo
{
    #[ApiField('角色 ID', example: 1)]
    public int $id;

    #[ApiField('角色名')]
    public string $name;

    #[ApiField('角色编码', example: 'super_admin')]
    public string $code;

    #[ApiField('数据权限范围,all/self/dept/dept_tree/custom')]
    public string $data_scope;
}
