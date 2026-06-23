<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 直传凭证。
 * Author: qiufeng
 */
final class AttachmentPolicyVo extends BaseVo
{
    #[ApiField('磁盘,local/aliyun/cos/qiniu', example: 'aliyun')]
    public string $disk;

    #[ApiField('上传模式,direct=表单直传云端 server=传后端 upload 接口', example: 'direct')]
    public string $mode;

    #[ApiField('对象键,登记时原样带回')]
    public string $key;

    #[ApiField('上传地址')]
    public string $host;

    #[ApiField('直传需携带的表单字段(file 字段放最后)')]
    public object $form;

    #[ApiField('凭证有效期(秒)', example: 600)]
    public int $expires_in;
}
