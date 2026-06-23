<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 字典类型行。
 * Author: qiufeng
 */
final class DictTypeVo extends BaseVo
{
    #[ApiField('字典类型 ID', example: 1)]
    public int $id;

    #[ApiField('字典名称')]
    public string $name;

    #[ApiField('字典编码', example: 'user_status')]
    public string $code;

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
