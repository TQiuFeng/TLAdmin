<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 岗位下拉框选项。
 * Author: qiufeng
 */
final class PostOptionVo extends BaseVo
{
    #[ApiField('岗位 ID', example: 1)]
    public int $id;

    #[ApiField('岗位名称')]
    public string $name;

    #[ApiField('岗位编码')]
    public string $code;
}
