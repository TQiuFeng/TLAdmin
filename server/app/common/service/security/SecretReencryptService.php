<?php

namespace app\common\service\security;

use app\common\config\ConfigRepository;
use app\common\support\AppKey;
use think\facade\Db;

/**
 * 用当前 APP_KEY 重新加密存量密文:第三方配置里的密钥、管理员的动态验证码密钥。
 *
 * 场景:从旧版本升级(旧版实际用公开的默认密钥加密)、或更换 APP_KEY 后把旧密文换成新密钥。
 * 当前密钥已能解开的跳过;解不开的依次尝试 --from 给的旧密钥和旧版默认密钥。
 * Author: qiufeng
 */
final class SecretReencryptService
{
    private const PREFIX = 'enc:v1:';

    public function __construct(private readonly ConfigRepository $config)
    {
    }

    /**
     * @param string[] $previousKeys 更换前的旧 APP_KEY
     * @return array{config: int, totp: int, failed: string[]} 各类重新加密的条数与失败项
     */
    public function run(array $previousKeys = []): array
    {
        $config = $this->reencryptConfig($previousKeys);
        $totp = $this->reencryptTotp($previousKeys);

        return ['config' => $config['count'], 'totp' => $totp['count'], 'failed' => [...$config['failed'], ...$totp['failed']]];
    }

    /**
     * 第三方配置里的密文。
     *
     * @return array{count: int, failed: string[]}
     */
    public function reencryptConfig(array $previousKeys = []): array
    {
        $result = ['count' => 0, 'failed' => []];
        $integration = (array) $this->config->get('integration.config', []);
        $changed = false;
        array_walk_recursive($integration, function (mixed &$value, string|int $key) use ($previousKeys, &$result, &$changed): void {
            if (!is_string($value) || !str_starts_with($value, self::PREFIX)) {
                return;
            }
            $cipher = substr($value, strlen(self::PREFIX));
            if (AppKey::isCurrent($cipher)) {
                return;
            }
            try {
                $value = self::PREFIX . AppKey::encrypt(AppKey::decrypt($cipher, $previousKeys));
                $result['count']++;
                $changed = true;
            } catch (\Throwable) {
                $result['failed'][] = "第三方配置 {$key}";
            }
        });
        if ($changed) {
            $this->config->set('integration.config', $integration);
            $this->config->save();
        }

        return $result;
    }

    /**
     * 管理员的动态验证码密钥。
     *
     * @return array{count: int, failed: string[]}
     */
    public function reencryptTotp(array $previousKeys = []): array
    {
        $result = ['count' => 0, 'failed' => []];
        $rows = Db::table('tl_admin_user')->where('totp_secret', 'like', self::PREFIX . '%')->field('id, username, totp_secret')->select();
        foreach ($rows as $row) {
            $cipher = substr((string) $row['totp_secret'], strlen(self::PREFIX));
            if (AppKey::isCurrent($cipher)) {
                continue;
            }
            try {
                Db::table('tl_admin_user')->where('id', $row['id'])->update([
                    'totp_secret' => self::PREFIX . AppKey::encrypt(AppKey::decrypt($cipher, $previousKeys)),
                ]);
                $result['count']++;
            } catch (\Throwable) {
                $result['failed'][] = "管理员 {$row['username']} 的动态验证码";
            }
        }

        return $result;
    }
}
