<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 字典项下拉框选项(按编码查询)。
 * Author: qiufeng
 */
final class DictOptionVo extends BaseVo
{
    #[ApiField('字典项 ID', example: 1)]
    public int $id;

    #[ApiField('显示标签')]
    public string $label;

    #[ApiField('字典值')]
    public string $value;

    #[ApiField('排序值')]
    public int $sort;

    #[ApiField('备注')]
    public string $remark;
}
