<?php

namespace app\common\service\integration;

use app\common\cache\RedisClient;
use app\common\exception\BizException;
use app\common\support\Tools;

/**
 * 存储统一服务边界。
 *
 * 上传全部走前端直传:本服务只负责签发直传凭证(阿里云 OSS PostObject、
 * 腾讯云 COS POST、七牛 uploadToken)、登记票据校验、远端删除和私有桶临时 URL。
 * 本地磁盘无法直传,由 AttachmentService 走后端接收兜底。
 * 底层全部使用现成厂商 SDK,不自研存储实现。
 * Author: qiufeng
 */
final class StorageService
{
    public const POLICY_TTL = 3600;

    public function __construct(private readonly IntegrationConfigService $configService)
    {
    }

    public function status(): array
    {
        return [
            'packages' => $this->configService->detail('storage')['packages'],
            'config' => $this->configService->detail('storage')['config'],
        ];
    }

    public function config(): array
    {
        return $this->configService->rawConfig('storage');
    }

    public function availableDrivers(): array
    {
        return [
            'local' => true,
            'aliyun' => class_exists(\OSS\OssClient::class),
            'cos' => class_exists(\Qcloud\Cos\Client::class),
            'qiniu' => class_exists(\Qiniu\Auth::class),
        ];
    }

    public function defaultDisk(): string
    {
        return (string) ($this->config()['default'] ?? 'local');
    }

    /**
     * 签发直传凭证。
     *
     * 返回统一结构:
     *   disk        磁盘名
     *   mode        direct(表单直传云端)/ server(本地兜底,传后端 upload 接口)
     *   key         对象键,登记时原样带回
     *   host        上传地址
     *   form        直传需携带的表单字段(file 字段放最后)
     *   expires_in  凭证有效期秒
     */
    public function buildUploadPolicy(string $filename, int $size, string $mime, int $userId): array
    {
        $config = $this->config();
        $disk = $this->defaultDisk();

        $this->assertUploadAllowed($filename, $size, $config);

        $ext = Tools::extension($filename);
        $key = sprintf('uploads/%s/%s.%s', date('Ym'), Tools::uuidCompact(), $ext);

        $policy = match ($disk) {
            'aliyun' => $this->aliyunPolicy($key, $size, $config),
            'cos' => $this->cosPolicy($key, $size, $config),
            'qiniu' => $this->qiniuPolicy($key, $config),
            default => $this->localPolicy($key),
        };

        // 上传票据:登记接口凭 key 换票,防止伪造登记
        RedisClient::set('upload:ticket:' . sha1($key), json_encode([
            'user_id' => $userId,
            'name' => mb_substr($filename, 0, 191),
            'size' => $size,
            'mime' => mb_substr($mime, 0, 128),
            'disk' => $disk,
            'key' => $key,
        ], JSON_UNESCAPED_UNICODE) ?: '{}', self::POLICY_TTL);

        return $policy + ['disk' => $disk, 'key' => $key, 'expires_in' => self::POLICY_TTL];
    }

    /** 消费上传票据,返回票据内容;不存在则视为伪造或过期 */
    public function consumeTicket(string $key): array
    {
        $redisKey = 'upload:ticket:' . sha1($key);
        $raw = RedisClient::get($redisKey);
        if ($raw === null) {
            throw BizException::paramError('上传凭证不存在或已过期,请重新获取直传凭证');
        }

        RedisClient::delete($redisKey);

        return json_decode($raw, true) ?: [];
    }

    /** 对象的公开访问 URL(私有桶请用 temporaryUrl) */
    public function publicUrl(string $disk, string $key): string
    {
        $disks = $this->config()['disks'] ?? [];

        return match ($disk) {
            'aliyun' => $this->aliyunHost($disks['aliyun'] ?? []) . '/' . $key,
            'cos' => $this->cosHost($disks['cos'] ?? []) . '/' . $key,
            'qiniu' => rtrim((string) ($disks['qiniu']['domain'] ?? ''), '/') . '/' . $key,
            default => rtrim((string) ($disks['local']['url_prefix'] ?? '/storage'), '/') . '/' . $key,
        };
    }

    /** 私有桶临时访问 URL;公开桶直接返回公开 URL */
    public function temporaryUrl(string $disk, string $key, int $expires = 600): string
    {
        $disks = $this->config()['disks'] ?? [];
        $diskConfig = $disks[$disk] ?? [];

        if (!($diskConfig['private'] ?? false)) {
            return $this->publicUrl($disk, $key);
        }

        return match ($disk) {
            'aliyun' => $this->aliyunClient($diskConfig)->signUrl((string) $diskConfig['bucket'], $key, $expires),
            'cos' => (string) $this->cosClient($diskConfig)->getObjectUrl(
                (string) $diskConfig['bucket'],
                $key,
                '+' . $expires . ' seconds'
            ),
            'qiniu' => $this->qiniuAuth($diskConfig)->privateDownloadUrl(
                rtrim((string) ($diskConfig['domain'] ?? ''), '/') . '/' . $key,
                $expires
            ),
            default => $this->publicUrl($disk, $key),
        };
    }

    /** 删除远端对象;返回是否删除成功(失败不抛异常,由调用方决定提示) */
    public function deleteObject(string $disk, string $key, string $localRootPath): bool
    {
        $disks = $this->config()['disks'] ?? [];
        $diskConfig = $disks[$disk] ?? [];

        try {
            switch ($disk) {
                case 'aliyun':
                    $this->aliyunClient($diskConfig)->deleteObject((string) $diskConfig['bucket'], $key);
                    return true;
                case 'cos':
                    $this->cosClient($diskConfig)->deleteObject([
                        'Bucket' => (string) $diskConfig['bucket'],
                        'Key' => $key,
                    ]);
                    return true;
                case 'qiniu':
                    $manager = new \Qiniu\Storage\BucketManager($this->qiniuAuth($diskConfig));
                    [, $error] = $manager->delete((string) $diskConfig['bucket'], $key);
                    return $error === null;
                default:
                    $file = rtrim($localRootPath, '/') . '/' . Tools::assertSafePath($key);
                    return !is_file($file) || unlink($file);
            }
        } catch (\Throwable) {
            return false;
        }
    }

    /** 校验扩展名白名单和大小限制,本地兜底上传同样调用 */
    public function assertUploadAllowed(string $filename, int $size, ?array $config = null): void
    {
        $config ??= $this->config();

        $ext = Tools::extension($filename);
        if ($ext === '') {
            throw BizException::paramError('文件缺少扩展名');
        }

        $allowed = array_filter(array_map('trim', explode(',', strtolower(
            (string) ($config['upload']['allowed_exts'] ?? '')
        ))));
        if ($allowed !== [] && !in_array($ext, $allowed, true)) {
            throw BizException::paramError("不允许上传 .{$ext} 文件");
        }

        $maxBytes = ((int) ($config['upload']['max_size_mb'] ?? 20)) * 1024 * 1024;
        if ($size <= 0 || $size > $maxBytes) {
            throw BizException::paramError('文件大小超出限制(最大 ' . Tools::humanSize($maxBytes) . ')');
        }
    }

    // ---- 阿里云 OSS PostObject ----

    private function aliyunPolicy(string $key, int $size, array $config): array
    {
        $diskConfig = $config['disks']['aliyun'] ?? [];
        $accessKeyId = (string) ($diskConfig['access_key_id'] ?? '');
        $accessKeySecret = (string) ($diskConfig['access_key_secret'] ?? '');
        if ($accessKeyId === '' || $accessKeySecret === '') {
            throw BizException::conflict('阿里云 OSS 未配置 AccessKey,请先到存储配置页填写');
        }

        $policy = base64_encode(json_encode([
            'expiration' => gmdate('Y-m-d\TH:i:s\Z', time() + self::POLICY_TTL),
            'conditions' => [
                ['eq', '$key', $key],
                ['content-length-range', 0, $size],
                ['bucket' => (string) ($diskConfig['bucket'] ?? '')],
            ],
        ]) ?: '');

        return [
            'mode' => 'direct',
            'host' => $this->aliyunHost($diskConfig),
            'form' => [
                'key' => $key,
                'policy' => $policy,
                'OSSAccessKeyId' => $accessKeyId,
                'signature' => base64_encode(hash_hmac('sha1', $policy, $accessKeySecret, true)),
                'success_action_status' => '200',
            ],
        ];
    }

    private function aliyunHost(array $diskConfig): string
    {
        $domain = trim((string) ($diskConfig['domain'] ?? ''));
        if ($domain !== '') {
            return rtrim($domain, '/');
        }

        $endpoint = preg_replace('#^https?://#', '', (string) ($diskConfig['endpoint'] ?? ''));

        return 'https://' . ($diskConfig['bucket'] ?? '') . '.' . $endpoint;
    }

    private function aliyunClient(array $diskConfig): \OSS\OssClient
    {
        return new \OSS\OssClient(
            (string) ($diskConfig['access_key_id'] ?? ''),
            (string) ($diskConfig['access_key_secret'] ?? ''),
            (string) ($diskConfig['endpoint'] ?? '')
        );
    }

    // ---- 腾讯云 COS PostObject ----

    private function cosPolicy(string $key, int $size, array $config): array
    {
        $diskConfig = $config['disks']['cos'] ?? [];
        $secretId = (string) ($diskConfig['secret_id'] ?? '');
        $secretKey = (string) ($diskConfig['secret_key'] ?? '');
        if ($secretId === '' || $secretKey === '') {
            throw BizException::conflict('腾讯云 COS 未配置密钥,请先到存储配置页填写');
        }

        $keyTime = time() . ';' . (time() + self::POLICY_TTL);
        $policyJson = json_encode([
            'expiration' => gmdate('Y-m-d\TH:i:s\Z', time() + self::POLICY_TTL),
            'conditions' => [
                ['eq', '$key', $key],
                ['content-length-range', 0, $size],
                ['q-sign-algorithm' => 'sha1'],
                ['q-ak' => $secretId],
                ['q-sign-time' => $keyTime],
            ],
        ]) ?: '';

        $signKey = hash_hmac('sha1', $keyTime, $secretKey);
        $signature = hash_hmac('sha1', sha1($policyJson), $signKey);

        return [
            'mode' => 'direct',
            'host' => $this->cosHost($diskConfig),
            'form' => [
                'key' => $key,
                'policy' => base64_encode($policyJson),
                'q-sign-algorithm' => 'sha1',
                'q-ak' => $secretId,
                'q-key-time' => $keyTime,
                'q-signature' => $signature,
                'success_action_status' => '200',
            ],
        ];
    }

    private function cosHost(array $diskConfig): string
    {
        $domain = trim((string) ($diskConfig['domain'] ?? ''));
        if ($domain !== '') {
            return rtrim($domain, '/');
        }

        return sprintf(
            'https://%s.cos.%s.myqcloud.com',
            (string) ($diskConfig['bucket'] ?? ''),
            (string) ($diskConfig['region'] ?? '')
        );
    }

    private function cosClient(array $diskConfig): \Qcloud\Cos\Client
    {
        return new \Qcloud\Cos\Client([
            'region' => (string) ($diskConfig['region'] ?? ''),
            'schema' => 'https',
            'credentials' => [
                'secretId' => (string) ($diskConfig['secret_id'] ?? ''),
                'secretKey' => (string) ($diskConfig['secret_key'] ?? ''),
            ],
        ]);
    }

    // ---- 七牛 Kodo ----

    private function qiniuPolicy(string $key, array $config): array
    {
        $diskConfig = $config['disks']['qiniu'] ?? [];
        if (($diskConfig['access_key'] ?? '') === '' || ($diskConfig['secret_key'] ?? '') === '') {
            throw BizException::conflict('七牛云未配置密钥,请先到存储配置页填写');
        }

        $token = $this->qiniuAuth($diskConfig)->uploadToken(
            (string) ($diskConfig['bucket'] ?? ''),
            $key,
            self::POLICY_TTL,
            ['insertOnly' => 1]
        );

        return [
            'mode' => 'direct',
            'host' => rtrim((string) ($diskConfig['upload_host'] ?? '') ?: 'https://upload.qiniup.com', '/'),
            'form' => [
                'key' => $key,
                'token' => $token,
            ],
        ];
    }

    private function qiniuAuth(array $diskConfig): \Qiniu\Auth
    {
        return new \Qiniu\Auth(
            (string) ($diskConfig['access_key'] ?? ''),
            (string) ($diskConfig['secret_key'] ?? '')
        );
    }

    // ---- 本地磁盘(无法直传,走后端 upload 接口) ----

    private function localPolicy(string $key): array
    {
        return [
            'mode' => 'server',
            'host' => '/adminapi/attachments/upload',
            'form' => ['key' => $key],
        ];
    }
}
