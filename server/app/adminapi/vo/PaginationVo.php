<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 分页信息。
 * Author: qiufeng
 */
final class PaginationVo extends BaseVo
{
    #[ApiField('当前页', example: 1)]
    public int $page;

    #[ApiField('每页数量', example: 20)]
    public int $page_size;

    #[ApiField('总条数', example: 100)]
    public int $total;
}
