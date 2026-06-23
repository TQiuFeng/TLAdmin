<?php

namespace app\common\config;

/**
 * 简单配置仓库。
 *
 * 合并默认配置文件和 runtime 配置文件，当前用于早期框架骨架阶段替代完整 ThinkPHP 配置服务。
 * Author: qiufeng
 */
final class ConfigRepository
{
    private array $items = [];

    public function __construct(
        private readonly array $defaultConfigFiles,
        private readonly string $runtimeConfigFile
    ) {
        $this->loadDefaults();
        $this->loadRuntime();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = $this->items;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        $segments = explode('.', $key);
        $cursor = &$this->items;

        foreach ($segments as $segment) {
            if (!isset($cursor[$segment]) || !is_array($cursor[$segment])) {
                $cursor[$segment] = [];
            }

            $cursor = &$cursor[$segment];
        }

        $cursor = $value;
    }

    public function save(): void
    {
        $directory = dirname($this->runtimeConfigFile);

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents(
            $this->runtimeConfigFile,
            json_encode($this->items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    public function all(): array
    {
        return $this->items;
    }

    private function loadDefaults(): void
    {
        foreach ($this->defaultConfigFiles as $file) {
            if (!is_file($file)) {
                continue;
            }

            $config = require $file;
            if (is_array($config)) {
                $this->items = array_replace_recursive($this->items, $config);
            }
        }
    }

    private function loadRuntime(): void
    {
        if (!is_file($this->runtimeConfigFile)) {
            return;
        }

        $decoded = json_decode((string) file_get_contents($this->runtimeConfigFile), true);
        if (is_array($decoded)) {
            $this->items = array_replace_recursive($this->items, $decoded);
        }
    }
}
