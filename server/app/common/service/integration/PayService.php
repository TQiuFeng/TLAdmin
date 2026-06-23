<?php

namespace app\common\service\integration;

/**
 * 支付统一服务边界。
 *
 * 底层使用 yansongda/pay，业务模块只能依赖本服务，不直接散落调用支付包。
 * Author: qiufeng
 */
final class PayService
{
    public function __construct(private readonly IntegrationConfigService $configService)
    {
    }

    public function status(): array
    {
        return [
            'package' => 'yansongda/pay',
            'class' => \Yansongda\Pay\Pay::class,
            'installed' => class_exists(\Yansongda\Pay\Pay::class),
            'config' => $this->configService->detail('pay')['config'],
        ];
    }

    public function config(): array
    {
        return $this->configService->rawConfig('pay');
    }

    public function assertInstalled(): void
    {
        if (!class_exists(\Yansongda\Pay\Pay::class)) {
            throw new \RuntimeException('缺少 Composer 包：yansongda/pay');
        }
    }
}
