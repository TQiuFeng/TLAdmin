<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 演示工具箱 · 二维码。
 * Author: qiufeng
 */
final class ToolboxQrcodeVo extends BaseVo
{
    #[ApiField('二维码内容', example: 'https://gitee.com/QiuTianLuoYe/tongliao')]
    public string $content;

    #[ApiField('PNG 图片 data URI,可直接放进 img src', example: 'data:image/png;base64,...')]
    public string $image;
}
