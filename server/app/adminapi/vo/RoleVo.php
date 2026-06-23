<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 角色行。
 * Author: qiufeng
 */
class RoleVo extends BaseVo
{
    #[ApiField('角色 ID', example: 1)]
    public int $id;

    #[ApiField('角色名')]
    public string $name;

    #[ApiField('角色编码', example: 'super_admin')]
    public string $code;

    #[ApiField('数据权限范围,all/self/dept/dept_tree/custom')]
    public string $data_scope;

    #[ApiField('排序值')]
    public int $sort;

    #[ApiField('状态,1 启用 0 禁用', example: 1)]
    public int $status;

    #[ApiField('备注')]
    public string $remark;

    #[ApiField('创建时间戳')]
    public int $create_time;

    #[ApiField('更新时间戳')]
    public int $update_time;

    #[ApiField('软删除时间戳,正常数据为 null')]
    public ?int $delete_time;
}
