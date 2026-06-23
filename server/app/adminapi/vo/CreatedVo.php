<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 创建成功返回。
 * Author: qiufeng
 */
final class CreatedVo extends BaseVo
{
    #[ApiField('新记录 ID', example: 1)]
    public int $id;
}
