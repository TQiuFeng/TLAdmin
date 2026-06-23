<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 可生成代码的数据库表。
 * Author: qiufeng
 */
final class GenTableVo extends BaseVo
{
    #[ApiField('表名', example: 'tl_demo_product')]
    public string $name;

    #[ApiField('表注释', example: '演示商品')]
    public string $comment;

    #[ApiField('大致行数')]
    public int $rows;

    #[ApiField('字段数')]
    public int $columns;
}
