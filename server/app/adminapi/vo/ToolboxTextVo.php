<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 文字处理结果。
 * Author: qiufeng
 */
final class ToolboxTextVo extends BaseVo
{
    #[ApiField('原文', example: '高薪招聘,有意者加QQ')]
    public string $text;

    #[ApiField('带声调拼音', example: 'gāo xīn zhāo pìn')]
    public string $pinyin;

    #[ApiField('无声调拼音', example: 'gao xin zhao pin')]
    public string $pinyin_plain;

    #[ApiField('拼音首字母', example: 'gxzp')]
    public string $abbr;

    #[ApiField('URL 别名', example: 'gao-xin-zhao-pin')]
    public string $slug;

    #[ApiField('繁体', example: '高薪招聘')]
    public string $traditional;

    #[ApiField('敏感词检测')]
    public ToolboxSensitiveVo $sensitive;
}
