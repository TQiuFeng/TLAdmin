<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 角色下拉框选项。
 * Author: qiufeng
 */
final class RoleOptionVo extends BaseVo
{
    #[ApiField('角色 ID', example: 1)]
    public int $id;

    #[ApiField('角色名')]
    public string $name;

    #[ApiField('角色编码')]
    public string $code;
}
