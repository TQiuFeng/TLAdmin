<?php

namespace app\common\service\integration;

/**
 * 微信公众号统一服务边界。
 *
 * 底层使用 w7corp/easywechat，封装公众号配置和应用实例创建。
 * Author: qiufeng
 */
final class WechatOfficialAccountService
{
    public function __construct(private readonly IntegrationConfigService $configService)
    {
    }

    public function status(): array
    {
        return [
            'package' => 'w7corp/easywechat',
            'class' => \EasyWeChat\OfficialAccount\Application::class,
            'installed' => class_exists(\EasyWeChat\OfficialAccount\Application::class),
            'config' => $this->configService->detail('wechat')['config'],
        ];
    }

    public function application(): object
    {
        $this->assertInstalled();

        $app = new \EasyWeChat\OfficialAccount\Application($this->configService->rawConfig('wechat'));
        // 注入兼容 psr/simple-cache 3.0 的缓存,绕开 symfony/cache 5.4 自带 Psr16Cache 的版本冲突
        $app->setCache(new \app\common\cache\Psr16Cache());

        return $app;
    }

    public function assertInstalled(): void
    {
        if (!class_exists(\EasyWeChat\OfficialAccount\Application::class)) {
            throw new \RuntimeException('缺少 Composer 包：w7corp/easywechat');
        }
    }
}
