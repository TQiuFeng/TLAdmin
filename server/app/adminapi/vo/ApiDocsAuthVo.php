<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 接口文档访问账号配置。
 *
 * 出于安全不回显密码明文,只返回是否已设置密码。
 * Author: qiufeng
 */
final class ApiDocsAuthVo extends BaseVo
{
    #[ApiField('是否开启接口文档账号密码保护', example: false)]
    public bool $enabled;

    #[ApiField('访问账号', example: 'admin')]
    public string $username;

    #[ApiField('是否已设置密码', example: true)]
    public bool $has_password;
}
