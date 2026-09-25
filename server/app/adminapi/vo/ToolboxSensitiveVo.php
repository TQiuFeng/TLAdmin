<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 文字处理 · 敏感词检测结果。
 * Author: qiufeng
 */
final class ToolboxSensitiveVo extends BaseVo
{
    #[ApiField('是否命中', example: true)]
    public bool $hit;

    #[ApiField('命中项', listOf: ToolboxSensitiveMatchVo::class)]
    public array $matches;

    #[ApiField('替换成 * 后的文本', example: '高薪**,***加**')]
    public string $replaced;
}
