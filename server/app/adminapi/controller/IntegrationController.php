<?php

namespace app\adminapi\controller;

use app\adminapi\vo\IntegrationDetailVo;
use app\adminapi\vo\IntegrationIndexVo;
use app\common\exception\BizException;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\RestController;
use app\common\service\integration\IntegrationConfigService;
use app\common\service\integration\PayService;
use app\common\service\integration\SmsService;
use app\common\service\integration\StorageService;
use app\common\service\integration\WechatMiniAppService;
use app\common\service\integration\WechatOfficialAccountService;

/**
 * 第三方能力配置接口。
 *
 * 给后台配置页和 Swagger UI 调试台使用，统一读取/保存支付、微信、短信、存储配置。
 * Author: qiufeng
 */
#[RestController('/adminapi/integrations', tag: '第三方能力')]
final class IntegrationController extends BaseController
{
    public function __construct(
        \app\common\response\ApiResponseFactory $responseFactory,
        \app\common\config\ConfigRepository $config,
        private readonly IntegrationConfigService $integrationConfig,
        private readonly PayService $payService,
        private readonly WechatOfficialAccountService $wechatService,
        private readonly WechatMiniAppService $wechatMiniAppService,
        private readonly SmsService $smsService,
        private readonly StorageService $storageService
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[GetMapping(permission: 'system:config:list', summary: '第三方能力和依赖包状态')]
    public function index(): IntegrationIndexVo
    {
        return IntegrationIndexVo::from([
            'list' => $this->integrationConfig->list(),
            'services' => [
                'pay' => $this->payService->status(),
                'wechat' => $this->wechatService->status(),
                'wechat_miniapp' => $this->wechatMiniAppService->status(),
                'sms' => $this->smsService->status(),
                'storage' => $this->storageService->status(),
            ],
        ]);
    }

    #[GetMapping('/config', permission: 'system:config:list', summary: '查看第三方配置(密钥脱敏)')]
    public function show(): IntegrationDetailVo
    {
        try {
            return IntegrationDetailVo::from(
                $this->integrationConfig->detail((string) $this->query('group', ''))
            );
        } catch (\InvalidArgumentException $exception) {
            throw BizException::paramError($exception->getMessage());
        }
    }

    #[PostMapping('/config', permission: 'system:config:update', summary: '保存第三方配置', message: '保存成功')]
    public function save(): IntegrationDetailVo
    {
        $group = (string) $this->input('group', '');
        $config = $this->input('config', []);

        if (!is_array($config)) {
            throw BizException::paramError('config 必须是对象');
        }

        try {
            return IntegrationDetailVo::from($this->integrationConfig->save($group, $config));
        } catch (\InvalidArgumentException $exception) {
            throw BizException::paramError($exception->getMessage());
        }
    }
}
