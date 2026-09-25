<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示客户行。
 * Author: qiufeng(代码生成器)
 */
final class DemoCustomerVo extends BaseVo
{
    #[ApiField('id')]
    public int $id;

    #[ApiField('客户名称')]
    public string $name;

    #[ApiField('联系人')]
    public string $contact;

    #[ApiField('手机号')]
    public string $mobile;

    #[ApiField('客户来源')]
    public string $source;

    #[ApiField('客户等级(1-5)')]
    public int $level;

    #[ApiField('下次跟进')]
    public int $next_follow_time;

    #[ApiField('状态:1启用 0禁用')]
    public int $status;

    #[ApiField('备注')]
    public string $remark;

    #[ApiField('create_time')]
    public int $create_time;

    #[ApiField('update_time')]
    public int $update_time;
}
