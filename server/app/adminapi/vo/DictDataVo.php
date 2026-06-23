<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 字典数据行。
 * Author: qiufeng
 */
final class DictDataVo extends BaseVo
{
    #[ApiField('字典项 ID', example: 1)]
    public int $id;

    #[ApiField('所属字典类型编码', example: 'user_status')]
    public string $type_code;

    #[ApiField('显示标签')]
    public string $label;

    #[ApiField('字典值')]
    public string $value;

    #[ApiField('排序值')]
    public int $sort;

    #[ApiField('状态,1 启用 0 禁用', example: 1)]
    public int $status;

    #[ApiField('备注')]
    public string $remark;

    #[ApiField('创建时间戳')]
    public int $create_time;

    #[ApiField('更新时间戳')]
    public int $update_time;

    #[ApiField('软删除时间戳,正常数据为 null')]
    public ?int $delete_time;
}
