<?php

/**
 * APP_KEY:能读到 .env 的值;旧版默认密钥加密的数据能解开并重新加密;生产环境不设密钥直接报错。
 * 用例里临时改 $_ENV,结束后还原。
 * Author: qiufeng
 */

use app\common\config\ConfigRepository;
use app\common\support\AppKey;
use app\common\support\Tools;
use app\common\service\security\SecretReencryptService;

/** 临时设置环境变量,用例结束还原 */
$withEnv = static function (TestContext $t, array $values): void {
    foreach ($values as $name => $value) {
        $had = array_key_exists($name, $_ENV);
        $old = $_ENV[$name] ?? null;
        if ($value === null) {
            unset($_ENV[$name]);
        } else {
            $_ENV[$name] = $value;
        }
        $t->defer(static function () use ($name, $had, $old): void {
            if ($had) {
                $_ENV[$name] = $old;
            } else {
                unset($_ENV[$name]);
            }
        });
    }
};

return [
    '配置层能读到 .env 里的 APP_KEY(旧版用 getenv 读不到)' => static function (TestContext $t) use ($withEnv): void {
        $withEnv($t, ['APP_KEY' => 'test-key-from-env']);
        $config = require dirname(__DIR__) . '/config/app.php';
        $t->same('test-key-from-env', $config['app']['key']);
    },

    '旧版默认密钥加密的数据,换成真实密钥后仍能解开' => static function (TestContext $t) use ($withEnv): void {
        $legacy = Tools::encrypt('sk-legacy-secret', AppKey::LEGACY_DEV_KEY);
        $withEnv($t, ['APP_KEY' => 'base64:' . base64_encode(random_bytes(32))]);
        $t->same('sk-legacy-secret', AppKey::decrypt($legacy));
        $t->same(false, AppKey::isCurrent($legacy), '旧密文不是当前密钥加密的');
    },

    '生产环境没设 APP_KEY 直接报错,不退回公开默认值' => static function (TestContext $t) use ($withEnv): void {
        $withEnv($t, ['APP_KEY' => '', 'APP_ENV' => 'production']);
        $t->throws(RuntimeException::class, static fn () => AppKey::current());
    },

    '密钥不对时解密失败,不会返回乱码' => static function (TestContext $t) use ($withEnv): void {
        $cipher = Tools::encrypt('sk-real-secret', 'some-other-key');
        $withEnv($t, ['APP_KEY' => 'yet-another-key']);
        // 多试几次覆盖 AES-CBC 偶发"解密成功"的情况
        for ($i = 0; $i < 50; $i++) {
            $t->throws(RuntimeException::class, static fn () => AppKey::decrypt(Tools::encrypt('sk-real-secret-' . $i, 'some-other-key')));
        }
        $t->same('sk-real-secret', AppKey::decrypt($cipher, ['some-other-key']), '给出旧密钥后能解开');
    },

    'secrets:reencrypt 把第三方配置里的旧密文换成当前密钥' => static function (TestContext $t) use ($withEnv): void {
        $runtime = sys_get_temp_dir() . '/tladmin-reencrypt-' . bin2hex(random_bytes(4)) . '.json';
        $t->defer(static fn () => @unlink($runtime));
        file_put_contents($runtime, json_encode(['integration' => ['config' => ['sms' => ['gateways' => ['aliyun' => [
            'access_key_secret' => 'enc:v1:' . Tools::encrypt('sms-secret', AppKey::LEGACY_DEV_KEY),
        ]]]]]]));

        $withEnv($t, ['APP_KEY' => 'base64:' . base64_encode(random_bytes(32))]);
        $root = dirname(__DIR__);
        $files = [$root . '/config/app.php', $root . '/config/api.php', $root . '/config/integration.php', $root . '/config/security.php'];
        $config = new ConfigRepository($files, $runtime);

        // 只处理配置:不能碰数据库里真实的动态验证码(这里的 APP_KEY 是临时的)
        $result = (new SecretReencryptService($config))->reencryptConfig();
        $t->same(1, $result['count']);

        $reloaded = new ConfigRepository($files, $runtime);
        $stored = (string) $reloaded->get('integration.config.sms.gateways.aliyun.access_key_secret');
        $cipher = substr($stored, strlen('enc:v1:'));
        $t->same(true, AppKey::isCurrent($cipher), '已换成当前密钥');
        $t->same('sms-secret', AppKey::decrypt($cipher));
    },

    'key:generate 只在 APP_KEY 为空或 --force 时写入' => static function (TestContext $t): void {
        $env = sys_get_temp_dir() . '/tladmin-env-' . bin2hex(random_bytes(4));
        $t->defer(static fn () => @unlink($env));

        file_put_contents($env, "APP_NAME=TLAdmin\nAPP_KEY=\nDB_HOST=127.0.0.1\n");
        $t->same(true, AppKey::writeToEnvFile($env));
        $content = (string) file_get_contents($env);
        $t->true(preg_match('/^APP_KEY=base64:[A-Za-z0-9+\/=]{44}$/m', $content) === 1, '写入了 base64 密钥');
        $t->contains('DB_HOST=127.0.0.1', $content, '其他配置不动');

        $t->same(false, AppKey::writeToEnvFile($env), '已有值时不覆盖');
        $t->same($content, (string) file_get_contents($env));
        $t->same(true, AppKey::writeToEnvFile($env, true), '--force 覆盖');
    },
];
