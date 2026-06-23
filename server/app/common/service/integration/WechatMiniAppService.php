<?php

namespace app\common\service\integration;

/**
 * 微信小程序统一服务边界。
 *
 * 底层使用 w7corp/easywechat 的 MiniApp Application，业务代码不要直接 new SDK。
 * Author: qiufeng
 */
final class WechatMiniAppService
{
    public function __construct(private readonly IntegrationConfigService $configService)
    {
    }

    public function status(): array
    {
        return [
            'package' => 'w7corp/easywechat',
            'class' => \EasyWeChat\MiniApp\Application::class,
            'installed' => class_exists(\EasyWeChat\MiniApp\Application::class),
            'config' => $this->configService->detail('wechat_miniapp')['config'],
        ];
    }

    public function application(): object
    {
        $this->assertInstalled();

        $app = new \EasyWeChat\MiniApp\Application($this->configService->rawConfig('wechat_miniapp'));
        // 注入兼容 psr/simple-cache 3.0 的缓存,绕开 symfony/cache 5.4 自带 Psr16Cache 的版本冲突
        $app->setCache(new \app\common\cache\Psr16Cache());

        return $app;
    }

    /**
     * 用 wx.login 的临时 code 换取会话信息(jscode2session)。
     *
     * @param string $code 小程序端 wx.login 返回的 code
     * @return array{openid: string, session_key: string, unionid?: string} 微信返回的原始结构
     */
    public function codeToSession(string $code): array
    {
        $this->assertInstalled();

        /** @var \EasyWeChat\MiniApp\Application $app */
        $app = $this->application();

        return $app->getUtils()->codeToSession($code);
    }

    /**
     * 用手机号授权按钮(open-type="getPhoneNumber")返回的 code 换取手机号。
     *
     * @param string $code 小程序端 getPhoneNumber 回调里的 code
     * @return array{phone_info: array{phoneNumber: string, purePhoneNumber: string, countryCode: string}} 微信返回的原始结构
     */
    public function getPhoneNumber(string $code): array
    {
        $this->assertInstalled();

        /** @var \EasyWeChat\MiniApp\Application $app */
        $app = $this->application();

        return $app->getUtils()->getPhoneNumber($code);
    }

    public function assertInstalled(): void
    {
        if (!class_exists(\EasyWeChat\MiniApp\Application::class)) {
            throw new \RuntimeException('缺少 Composer 包：w7corp/easywechat');
        }
    }
}
