<?php

/**
 * ConfigRepository:runtime 文件只保存改过的值,代码定义的键不被旧文件覆盖。
 * Author: qiufeng
 */

use app\common\config\ConfigRepository;

$defaults = [
    dirname(__DIR__) . '/config/app.php',
    dirname(__DIR__) . '/config/api.php',
    dirname(__DIR__) . '/config/integration.php',
    dirname(__DIR__) . '/config/security.php',
];

/** 临时 runtime 文件,用例结束删除 */
$tempRuntime = static function (TestContext $t, ?array $content = null): string {
    $file = sys_get_temp_dir() . '/tladmin-config-test-' . bin2hex(random_bytes(4)) . '.json';
    if ($content !== null) {
        file_put_contents($file, json_encode($content, JSON_UNESCAPED_UNICODE));
    }
    $t->defer(static fn () => @unlink($file));

    return $file;
};

return [
    '没有 runtime 文件时全部取默认值' => static function (TestContext $t) use ($defaults, $tempRuntime): void {
        $config = new ConfigRepository($defaults, $tempRuntime($t));
        $t->same(\think\Filesystem::class, $config->get('integration.packages.filesystem.class'));
    },

    '保存只写改过的值' => static function (TestContext $t) use ($defaults, $tempRuntime): void {
        $file = $tempRuntime($t);
        $config = new ConfigRepository($defaults, $file);
        $config->set('security.rate_limit.per_second', 12);
        $config->save();

        $saved = json_decode((string) file_get_contents($file), true);
        $t->same(['security' => ['rate_limit' => ['per_second' => 12]]], $saved);
    },

    '旧版本整份写入的文件:丢弃依赖包定义,保留真正改过的值' => static function (TestContext $t) use ($defaults, $tempRuntime): void {
        $fresh = new ConfigRepository($defaults, $tempRuntime($t));
        $polluted = $fresh->all();
        // 模拟旧版本:依赖包检测类名还是错的,另外用户改过短信渠道
        $polluted['integration']['packages']['filesystem']['class'] = 'think\\filesystem\\Filesystem';
        $polluted['integration']['config']['sms']['default']['gateways'] = ['qcloud'];

        $file = $tempRuntime($t, $polluted);
        $config = new ConfigRepository($defaults, $file);
        $t->same(\think\Filesystem::class, $config->get('integration.packages.filesystem.class'), '依赖包定义应以代码为准');
        $t->same(['qcloud'], $config->get('integration.config.sms.default.gateways'), '用户设置应保留');

        $config->save();
        $saved = json_decode((string) file_get_contents($file), true);
        $t->true(!isset($saved['integration']['packages']), 'runtime 文件不应再有 packages');
        $t->true(!isset($saved['app']), '与默认值相同的部分不应写回');
        $t->same(['qcloud'], $saved['integration']['config']['sms']['default']['gateways'] ?? null);
    },

    '列表值整体覆盖,不逐项合并' => static function (TestContext $t) use ($defaults, $tempRuntime): void {
        $file = $tempRuntime($t);
        $config = new ConfigRepository($defaults, $file);
        $config->set('security.ip_block.rules', [['country' => '中国', 'province' => '', 'city' => '', 'ip' => '']]);
        $config->save();

        $reloaded = new ConfigRepository($defaults, $file);
        $t->same(1, count($reloaded->get('security.ip_block.rules', [])));
    },
];
