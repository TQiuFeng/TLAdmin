<?php

namespace app\common\support\tools;

/**
 * 文件工具。
 *
 * 封装扩展名、路径规范化、路径安全检查和文件大小格式化。
 * Author: qiufeng
 */
trait FileTools
{
    /**
     * 取文件扩展名(小写,不含点)。
     *
     * @param string $path 文件名或路径
     * @return string 扩展名,如 'jpg';无扩展名返回空串
     */
    public static function extension(string $path): string
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * 规范化路径:统一为 / 分隔,解析掉 . 与 ..,去除多余分隔符。
     *
     * @param string $path 原路径
     * @return string 规范化后的路径
     */
    public static function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $parts = [];

        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                array_pop($parts);
                continue;
            }
            $parts[] = $part;
        }

        return implode('/', $parts);
    }

    /**
     * 校验路径不含 ..(防目录穿越)。
     *
     * @param string $path 待校验的路径
     * @return string 规范化后的安全路径
     * @throws \InvalidArgumentException 路径含 .. 时
     */
    public static function assertSafePath(string $path): string
    {
        if (str_contains($path, '..')) {
            throw new \InvalidArgumentException('文件路径不安全');
        }

        return self::normalizePath($path);
    }

    /**
     * 字节数转人类可读大小,如 1024 → 1 KB。
     *
     * @param int $bytes     字节数
     * @param int $precision 保留的小数位,默认 2
     * @return string 带单位的大小(B/KB/MB/GB/TB)
     */
    public static function humanSize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = 0;
        $size = $bytes;

        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }

        return round($size, $precision) . ' ' . $units[$index];
    }
}
