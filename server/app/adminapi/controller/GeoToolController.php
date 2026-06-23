<?php

namespace app\adminapi\controller;

use app\adminapi\vo\IpLocationVo;
use app\adminapi\vo\PhoneLocationVo;
use app\common\router\GetMapping;
use app\common\router\RestController;
use app\common\service\geo\IpLocationService;
use app\common\service\geo\PhoneLocationService;

/**
 * 本地化归属地工具接口。
 *
 * 手机号和 IP 归属地查询均读取本地数据文件，不调用外部接口。
 * Author: qiufeng
 */
#[RestController('/adminapi/tools', tag: '工具')]
final class GeoToolController extends BaseController
{
    public function __construct(
        \app\common\response\ApiResponseFactory $responseFactory,
        \app\common\config\ConfigRepository $config,
        private readonly PhoneLocationService $phoneLocationService,
        private readonly IpLocationService $ipLocationService
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[GetMapping('/phone-location', summary: '手机号归属地查询(本地库)', query: [
        'phone' => ['type' => 'string', 'description' => '手机号(11 位)', 'required' => true, 'example' => '13800138000'],
    ])]
    public function phoneLocation(): PhoneLocationVo
    {
        return PhoneLocationVo::from(
            $this->phoneLocationService->lookup((string) $this->query('phone', ''))
        );
    }

    #[GetMapping('/ip-location', summary: 'IP 归属地查询(本地库)', query: [
        'ip' => ['type' => 'string', 'description' => 'IPv4 地址,不传则默认查询当前请求来源 IP', 'example' => '8.8.8.8'],
    ])]
    public function ipLocation(): IpLocationVo
    {
        $ip = (string) $this->query('ip', $this->request->clientIp());

        return IpLocationVo::from($this->ipLocationService->lookup($ip));
    }
}
