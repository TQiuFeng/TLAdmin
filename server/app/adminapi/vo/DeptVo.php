<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 部门节点(树形)。
 * Author: qiufeng
 */
final class DeptVo extends BaseVo
{
    #[ApiField('部门 ID', example: 1)]
    public int $id;

    #[ApiField('上级部门 ID,0 表示顶级')]
    public int $parent_id;

    #[ApiField('部门名称')]
    public string $name;

    #[ApiField('负责人')]
    public string $leader;

    #[ApiField('联系电话')]
    public string $phone;

    #[ApiField('邮箱')]
    public string $email;

    #[ApiField('排序值')]
    public int $sort;

    #[ApiField('状态,1 启用 0 禁用', example: 1)]
    public int $status;

    #[ApiField('创建时间戳')]
    public int $create_time;

    #[ApiField('更新时间戳')]
    public int $update_time;

    #[ApiField('软删除时间戳,正常数据为 null')]
    public ?int $delete_time;

    #[ApiField('子部门列表,结构同当前节点', listOf: DeptVo::class)]
    public array $children;
}
