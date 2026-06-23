<?php

namespace app\adminapi\controller;

use app\adminapi\vo\IpBlockCheckVo;
use app\adminapi\vo\IpBlockConfigVo;
use app\common\exception\BizException;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\RestController;
use app\common\service\geo\IpLocationService;
use app\common\service\security\IpBlockService;

/**
 * IP 归属地屏蔽配置接口。
 *
 * 提供规则读取、保存和单个 IP 调试检查能力，便于 Swagger UI 直接验证屏蔽结果。
 * Author: qiufeng
 */
#[RestController('/adminapi/security/ip-block', tag: '安全')]
final class IpBlockController extends BaseController
{
    public function __construct(
        \app\common\response\ApiResponseFactory $responseFactory,
        \app\common\config\ConfigRepository $config,
        private readonly IpBlockService $ipBlockService,
        private readonly IpLocationService $ipLocationService
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[GetMapping('/regions', permission: 'system:config:list', summary: '可选归属地级联(国家/省/市,供屏蔽规则下拉)')]
    public function regions(): array
    {
        return $this->ipLocationService->regions();
    }

    #[GetMapping('/config', permission: 'system:config:list', summary: '查看 IP 归属地屏蔽配置')]
    public function config(): IpBlockConfigVo
    {
        return IpBlockConfigVo::from($this->ipBlockService->config());
    }

    #[PostMapping('/config', permission: 'system:config:update', summary: '保存 IP 归属地屏蔽配置', message: '保存成功')]
    public function save(): IpBlockConfigVo
    {
        try {
            return IpBlockConfigVo::from($this->ipBlockService->save($this->request->all()));
        } catch (\InvalidArgumentException $exception) {
            throw BizException::paramError($exception->getMessage());
        }
    }

    #[GetMapping('/check', summary: '检查 IP 是否被屏蔽')]
    public function check(): IpBlockCheckVo
    {
        $config = $this->ipBlockService->config();
        $ip = (string) $this->query('ip', $this->request->clientIp((bool) $config['trust_proxy_headers']));

        return IpBlockCheckVo::from($this->ipBlockService->evaluateIp($ip, $config));
    }
}
