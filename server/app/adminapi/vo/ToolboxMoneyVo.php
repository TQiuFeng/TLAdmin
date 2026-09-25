<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 金额换算结果。
 * Author: qiufeng
 */
final class ToolboxMoneyVo extends BaseVo
{
    #[ApiField('输入金额(元)', example: '12345.67')]
    public string $amount;

    #[ApiField('中文大写', example: '壹万贰仟叁佰肆拾伍元陆角柒分')]
    public string $chinese;

    #[ApiField('换算成分', example: 1234567)]
    public int $fen;

    #[ApiField('展示格式', example: '¥12345.67')]
    public string $formatted;
}
