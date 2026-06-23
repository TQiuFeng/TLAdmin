<?php

namespace app\common\service\auth;

use app\common\cache\RedisClient;
use app\common\config\ConfigRepository;
use app\common\config\EnvLoader;
use app\common\exception\BizException;
use app\common\support\Tools;
use PragmaRX\Google2FA\Google2FA;
use think\facade\Db;

/**
 * Google Authenticator 动态验证码(TOTP)服务。
 *
 * 基于 pragmarx/google2fa(RFC 6238):
 * - setup 生成密钥暂存 Redis,confirm 验证一次成功后加密入库正式绑定;
 * - 全局开关 security.totp.enabled 开启后,已绑定账号登录必须校验动态码;
 * - 解绑需动态码确认,管理员可强制重置他人绑定。
 * Author: qiufeng
 */
final class TotpService
{
    private const SETUP_TTL = 600;
    private const ENCRYPT_PREFIX = 'enc:v1:';

    private Google2FA $google2fa;

    public function __construct(private readonly ConfigRepository $config)
    {
        $this->google2fa = new Google2FA();
    }

    public function globalEnabled(): bool
    {
        return (bool) $this->config->get('security.totp.enabled', false);
    }

    /** 当前用户绑定状态 */
    public function status(int $userId): array
    {
        $user = $this->mustFindUser($userId);

        return [
            'global_enabled' => $this->globalEnabled(),
            'bound' => (int) $user['totp_enabled'] === 1,
        ];
    }

    /** 生成待绑定密钥,暂存 Redis;前端用 otpauth_uri 渲染二维码 */
    public function setup(int $userId): array
    {
        $user = $this->mustFindUser($userId);

        if ((int) $user['totp_enabled'] === 1) {
            throw BizException::conflict('已绑定动态验证码,请先解绑');
        }

        $secret = $this->google2fa->generateSecretKey(32);
        RedisClient::set("totp:setup:{$userId}", $secret, self::SETUP_TTL);

        $issuer = (string) $this->config->get('security.totp.issuer', 'TLAdmin');

        return [
            'secret' => $secret,
            'otpauth_uri' => $this->google2fa->getQRCodeUrl($issuer, (string) $user['username'], $secret),
            'expires_in' => self::SETUP_TTL,
        ];
    }

    /** 用动态码确认绑定,密钥加密入库 */
    public function confirm(int $userId, string $code): void
    {
        $secret = RedisClient::get("totp:setup:{$userId}");
        if ($secret === null) {
            throw BizException::paramError('绑定会话已过期,请重新获取二维码');
        }

        if (!$this->verifyCode($secret, $code)) {
            throw BizException::paramError('动态验证码错误,请确认手机时间准确后重试');
        }

        Db::table('tl_admin_user')->where('id', $userId)->update([
            'totp_secret' => self::ENCRYPT_PREFIX . Tools::encrypt($secret, $this->secretKey()),
            'totp_enabled' => 1,
            'update_time' => time(),
        ]);

        RedisClient::delete("totp:setup:{$userId}");
    }

    /** 解绑:需当前动态码确认 */
    public function disable(int $userId, string $code): void
    {
        $user = $this->mustFindUser($userId);

        if ((int) $user['totp_enabled'] !== 1) {
            throw BizException::paramError('当前未绑定动态验证码');
        }

        if (!$this->verifyForUser($user, $code)) {
            throw BizException::paramError('动态验证码错误');
        }

        $this->clearBinding($userId);
    }

    /** 管理员强制重置他人绑定(忘记验证器场景) */
    public function resetForUser(int $targetUserId): void
    {
        $this->mustFindUser($targetUserId);
        $this->clearBinding($targetUserId);
    }

    /** 登录时校验:用户记录 + 动态码 */
    public function verifyForUser(array $user, string $code): bool
    {
        $stored = (string) ($user['totp_secret'] ?? '');
        if ($stored === '') {
            return false;
        }

        if (str_starts_with($stored, self::ENCRYPT_PREFIX)) {
            $stored = Tools::decrypt(substr($stored, strlen(self::ENCRYPT_PREFIX)), $this->secretKey());
        }

        return $this->verifyCode($stored, $code);
    }

    private function verifyCode(string $secret, string $code): bool
    {
        $code = trim($code);
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        // window=1:容忍前后各 30 秒的时钟偏差
        return $this->google2fa->verifyKey($secret, $code, 1) !== false;
    }

    private function clearBinding(int $userId): void
    {
        Db::table('tl_admin_user')->where('id', $userId)->update([
            'totp_secret' => '',
            'totp_enabled' => 0,
            'update_time' => time(),
        ]);
    }

    private function secretKey(): string
    {
        return (string) ($this->config->get('app.key') ?: EnvLoader::get('APP_KEY') ?: 'tladmin-local-dev-key');
    }

    private function mustFindUser(int $userId): array
    {
        $user = Db::table('tl_admin_user')->where('id', $userId)->whereNull('delete_time')->find();
        if (!$user) {
            throw BizException::notFound('账号不存在');
        }

        return $user;
    }
}
