<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示公告行。
 * Author: qiufeng(代码生成器)
 */
final class DemoNoticeVo extends BaseVo
{
    #[ApiField('id')]
    public int $id;

    #[ApiField('公告标题')]
    public string $title;

    #[ApiField('公告内容')]
    public string $content;

    #[ApiField('置顶:1是 0否')]
    public int $is_top;

    #[ApiField('生效时间')]
    public int $start_time;

    #[ApiField('失效时间')]
    public int $end_time;

    #[ApiField('状态:1启用 0禁用')]
    public int $status;

    #[ApiField('create_time')]
    public int $create_time;

    #[ApiField('update_time')]
    public int $update_time;
}
