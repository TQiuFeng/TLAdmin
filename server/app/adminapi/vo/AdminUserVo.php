<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 管理员列表行。
 * Author: qiufeng
 */
final class AdminUserVo extends BaseVo
{
    #[ApiField('管理员 ID', example: 1)]
    public int $id;

    #[ApiField('登录账号', example: 'admin')]
    public string $username;

    #[ApiField('昵称')]
    public string $nickname;

    #[ApiField('头像地址')]
    public string $avatar;

    #[ApiField('邮箱')]
    public string $email;

    #[ApiField('手机号')]
    public string $mobile;

    #[ApiField('部门 ID,0 表示未分配')]
    public int $dept_id;

    #[ApiField('部门名称')]
    public string $dept_name;

    #[ApiField('是否超级管理员,1 是 0 否', example: 0)]
    public int $is_super;

    #[ApiField('状态,1 启用 0 禁用', example: 1)]
    public int $status;

    #[ApiField('最后登录时间戳')]
    public int $last_login_time;

    #[ApiField('最后登录 IP')]
    public string $last_login_ip;

    #[ApiField('备注')]
    public string $remark;

    #[ApiField('创建时间戳')]
    public int $create_time;

    #[ApiField('拥有角色', listOf: RoleBriefVo::class)]
    public array $roles;
}
