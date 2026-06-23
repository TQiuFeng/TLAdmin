<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 附件访问 URL。
 * Author: qiufeng
 */
final class AttachmentUrlVo extends BaseVo
{
    #[ApiField('访问地址,私有桶为临时签名 URL')]
    public string $url;

    #[ApiField('签名有效期(秒),范围 60-86400', example: 600)]
    public int $expires_in;
}
