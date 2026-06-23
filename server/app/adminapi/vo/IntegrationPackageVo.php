<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * 第三方能力依赖包状态。
 * Author: qiufeng
 */
final class IntegrationPackageVo extends BaseVo
{
    #[ApiField('包标识', example: 'pay')]
    public string $key;

    #[ApiField('Composer 包名', example: 'yansongda/pay')]
    public string $name;

    #[ApiField('包用途说明')]
    public string $title;

    #[ApiField('探测类名')]
    public string $class;

    #[ApiField('是否已安装', example: true)]
    public bool $installed;
}
