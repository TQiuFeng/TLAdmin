<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 角色简要信息(管理员列表行内)。
 * Author: qiufeng
 */
final class RoleBriefVo extends BaseVo
{
    #[ApiField('角色 ID', example: 1)]
    public int $id;

    #[ApiField('角色名')]
    public string $name;
}
