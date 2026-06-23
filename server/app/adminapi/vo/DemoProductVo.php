<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示商品行。
 * Author: qiufeng(代码生成器)
 */
final class DemoProductVo extends BaseVo
{
    #[ApiField('id')]
    public int $id;

    #[ApiField('商品名称')]
    public string $name;

    #[ApiField('价格(分)')]
    public int $price;

    #[ApiField('库存')]
    public int $stock;

    #[ApiField('状态:1启用 0禁用')]
    public int $status;

    #[ApiField('备注')]
    public string $remark;

    #[ApiField('create_time')]
    public int $create_time;

    #[ApiField('update_time')]
    public int $update_time;
}
