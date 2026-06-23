<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 会员用户行。
 * Author: qiufeng
 */
final class UserVo extends BaseVo
{
    #[ApiField('会员 ID')]
    public int $id;

    #[ApiField('会员账号')]
    public string $username;

    #[ApiField('昵称')]
    public string $nickname;

    #[ApiField('头像')]
    public string $avatar;

    #[ApiField('手机号')]
    public string $mobile;

    #[ApiField('邮箱')]
    public string $email;

    #[ApiField('性别:0未知 1男 2女')]
    public int $gender;

    #[ApiField('状态:1正常 0禁用')]
    public int $status;

    #[ApiField('余额(分)')]
    public int $balance;

    #[ApiField('积分')]
    public int $points;

    #[ApiField('注册 IP')]
    public string $register_ip;

    #[ApiField('备注')]
    public string $remark;

    #[ApiField('最后登录时间')]
    public int $last_login_time;

    #[ApiField('注册时间')]
    public int $create_time;
}
