<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示文章行。
 * Author: qiufeng(代码生成器)
 */
final class DemoArticleVo extends BaseVo
{
    #[ApiField('id')]
    public int $id;

    #[ApiField('文章标题')]
    public string $title;

    #[ApiField('作者')]
    public string $author;

    #[ApiField('分类')]
    public string $category;

    #[ApiField('摘要')]
    public string $summary;

    #[ApiField('正文')]
    public string $content;

    #[ApiField('阅读量')]
    public int $views;

    #[ApiField('置顶:1是 0否')]
    public int $is_top;

    #[ApiField('发布时间')]
    public int $publish_time;

    #[ApiField('状态:1启用 0禁用')]
    public int $status;

    #[ApiField('create_time')]
    public int $create_time;

    #[ApiField('update_time')]
    public int $update_time;
}
