<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 当前用户档案的基础信息。
 * Author: qiufeng
 */
final class ProfileUserVo extends BaseVo
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

    #[ApiField('是否超级管理员,1 是 0 否', example: 1)]
    public int $is_super;

    #[ApiField('最后登录时间戳')]
    public int $last_login_time;

    #[ApiField('最后登录 IP')]
    public string $last_login_ip;
}
