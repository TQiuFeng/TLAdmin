<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 分页结果,类似 Java 的 PageResult<T>。
 *
 * 用法:PageVo::of($service->paginate(...), AdminUserVo::class)
 * Author: qiufeng
 */
final class PageVo extends BaseVo
{
    #[ApiField('数据列表')]
    public array $list;

    #[ApiField('分页信息')]
    public PaginationVo $pagination;

    /** @param class-string<BaseVo> $voClass 行 VO 类 */
    public static function of(array $result, string $voClass): self
    {
        $vo = new self();
        $vo->list = $voClass::list($result['list'] ?? []);
        $vo->pagination = PaginationVo::from($result['pagination'] ?? []);

        return $vo;
    }
}
