<?php

namespace app\adminapi\controller;

use app\adminapi\vo\HealthVo;
use app\common\router\GetMapping;
use app\common\router\RestController;

/**
 * 基础入口接口。
 *
 * 提供健康检查等框架级基础接口。
 * Author: qiufeng
 */
#[RestController('/adminapi', tag: '基础')]
final class IndexController extends BaseController
{
    #[GetMapping('/health', summary: '健康检查', auth: false)]
    public function health(): HealthVo
    {
        return HealthVo::from([
            'app' => $this->config->get('app.name', 'TLAdmin'),
            'env' => $this->config->get('app.env', 'local'),
            'api_response_format' => $this->config->get('api.response_format', 'json'),
            'status' => 'running',
        ]);
    }
}
