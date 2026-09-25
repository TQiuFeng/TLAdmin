<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示订单行。
 * Author: qiufeng(代码生成器)
 */
final class DemoOrderVo extends BaseVo
{
    #[ApiField('id')]
    public int $id;

    #[ApiField('订单号')]
    public string $order_no;

    #[ApiField('客户')]
    public string $customer_name;

    #[ApiField('商品')]
    public string $goods_name;

    #[ApiField('数量')]
    public int $quantity;

    #[ApiField('订单金额(元)')]
    public int $amount;

    #[ApiField('已支付:1是 0否')]
    public int $is_paid;

    #[ApiField('支付时间')]
    public int $pay_time;

    #[ApiField('备注')]
    public string $remark;

    #[ApiField('create_time')]
    public int $create_time;

    #[ApiField('update_time')]
    public int $update_time;
}
