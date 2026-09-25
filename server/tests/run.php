<?php

/**
 * 轻量测试运行器(不依赖 PHPUnit,仓库自带的 vendor 里没有它)。
 *
 * 用法:
 *   php tests/run.php              运行 tests/ 下全部 *Test.php
 *   php tests/run.php Generator    只运行文件名包含 Generator 的测试
 *
 * 每个 *Test.php 返回 [用例名 => callable(TestContext $t)]。
 * 需要数据库的用例先调用 $t->needsDb(),数据库连不上时该用例记为跳过而不是失败。
 * 全部通过退出码为 0,有失败为 1,方便接到 CI。
 * Author: qiufeng
 */

declare(strict_types=1);

$rootPath = dirname(__DIR__);
require __DIR__ . '/bootstrap.php';

$filter = $argv[1] ?? '';
$files = glob(__DIR__ . '/*Test.php') ?: [];
sort($files);

$passed = $failed = $skipped = 0;
$failures = [];

foreach ($files as $file) {
    $suite = basename($file, '.php');
    if ($filter !== '' && stripos($suite, $filter) === false) {
        continue;
    }

    $cases = require $file;
    echo "\n{$suite}\n";

    foreach ($cases as $name => $case) {
        $t = new TestContext();
        try {
            $case($t);
            $passed++;
            echo "  \033[32m✓\033[0m {$name}\n";
        } catch (TestSkipped $e) {
            $skipped++;
            echo "  \033[33m-\033[0m {$name}(跳过:{$e->getMessage()})\n";
        } catch (Throwable $e) {
            $failed++;
            $failures[] = "{$suite} › {$name}: {$e->getMessage()}";
            echo "  \033[31m✗\033[0m {$name}\n    {$e->getMessage()}\n";
        } finally {
            $t->cleanup();
        }
    }
}

echo "\n通过 {$passed},失败 {$failed},跳过 {$skipped}\n";
exit($failed > 0 ? 1 : 0);
