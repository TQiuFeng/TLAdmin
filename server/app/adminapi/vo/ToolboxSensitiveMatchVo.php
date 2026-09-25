<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 文字处理 · 敏感词命中项。
 * Author: qiufeng
 */
final class ToolboxSensitiveMatchVo extends BaseVo
{
    #[ApiField('词库里的规范词', example: '招聘')]
    public string $word;

    #[ApiField('原文片段', example: '招聘')]
    public string $text;

    #[ApiField('分类标识', example: 'ad')]
    public string $category;

    #[ApiField('分类中文名', example: '广告')]
    public string $category_label;

    #[ApiField('在原文中的字符偏移', example: 2)]
    public int $offset;
}
