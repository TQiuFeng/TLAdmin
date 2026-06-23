<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 附件删除结果。
 * Author: qiufeng
 */
final class AttachmentDeleteVo extends BaseVo
{
    #[ApiField('远端/本地物理文件是否删除成功,记录本身已软删', example: true)]
    public bool $remote_deleted;
}
