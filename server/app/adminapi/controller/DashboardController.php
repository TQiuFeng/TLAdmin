<?php

namespace app\adminapi\controller;

use app\adminapi\vo\DashboardOverviewVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\GetMapping;
use app\common\router\RestController;
use app\common\service\system\DashboardService;

/**
 * 仪表盘。登录即可访问,返回内容按当前账号的权限裁剪。
 * Author: qiufeng
 */
#[RestController('/adminapi/dashboard', tag: '仪表盘')]
final class DashboardController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly DashboardService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[GetMapping('/overview', summary: '仪表盘概览:统计卡片、近 7 天操作趋势、最近登录(按权限返回)')]
    public function overview(): DashboardOverviewVo
    {
        return DashboardOverviewVo::from($this->service->overview($this->authUserId()));
    }
}
