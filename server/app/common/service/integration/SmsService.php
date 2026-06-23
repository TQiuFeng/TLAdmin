<?php

namespace app\common\service\integration;

/**
 * 短信统一服务边界。
 *
 * 底层使用 overtrue/easy-sms，统一阿里云、腾讯云等短信渠道。
 * Author: qiufeng
 */
final class SmsService
{
    public function __construct(private readonly IntegrationConfigService $configService)
    {
    }

    public function status(): array
    {
        return [
            'package' => 'overtrue/easy-sms',
            'class' => \Overtrue\EasySms\EasySms::class,
            'installed' => class_exists(\Overtrue\EasySms\EasySms::class),
            'config' => $this->configService->detail('sms')['config'],
        ];
    }

    public function client(): object
    {
        $this->assertInstalled();

        return new \Overtrue\EasySms\EasySms($this->configService->rawConfig('sms'));
    }

    public function assertInstalled(): void
    {
        if (!class_exists(\Overtrue\EasySms\EasySms::class)) {
            throw new \RuntimeException('缺少 Composer 包：overtrue/easy-sms');
        }
    }
}
