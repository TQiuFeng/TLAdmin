<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 附件记录。
 * Author: qiufeng
 */
final class AttachmentVo extends BaseVo
{
    #[ApiField('附件 ID', example: 1)]
    public int $id;

    #[ApiField('原始文件名', example: 'avatar.png')]
    public string $name;

    #[ApiField('存储路径(对象键)')]
    public string $path;

    #[ApiField('存储磁盘,local/aliyun/cos/qiniu', example: 'local')]
    public string $disk;

    #[ApiField('公开访问地址,私有桶为空')]
    public string $url;

    #[ApiField('MIME 类型', example: 'image/png')]
    public string $mime;

    #[ApiField('扩展名', example: 'png')]
    public string $ext;

    #[ApiField('文件大小(字节)')]
    public int $size;

    #[ApiField('文件 SHA1')]
    public string $sha1;

    #[ApiField('上传人 ID')]
    public int $uploader_id;

    #[ApiField('上传时间戳')]
    public int $create_time;
}
