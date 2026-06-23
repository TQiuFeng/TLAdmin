<?php

namespace app\common\support\tools;

/**
 * 中文简繁体转换工具。
 *
 * 基于 overtrue/php-opencc（OpenCC 词库本地打包，离线可用），支持词组级转换，
 * 如「头发」→「頭髮」而不是逐字硬转。
 * Author: qiufeng
 */
trait ChineseTools
{
    /**
     * 简体转繁体。
     *
     * @param string $text    简体文本
     * @param string $variant 繁体变体:t 通用繁体(默认) / tw 台湾正体 / hk 香港繁体
     * @return string 繁体文本
     */
    public static function toTraditional(string $text, string $variant = 't'): string
    {
        self::assertOpenccInstalled();

        return match ($variant) {
            'tw' => \Overtrue\PHPOpenCC\OpenCC::s2twp($text),
            'hk' => \Overtrue\PHPOpenCC\OpenCC::s2hk($text),
            default => \Overtrue\PHPOpenCC\OpenCC::s2t($text),
        };
    }

    /**
     * 繁体转简体。
     *
     * @param string $text    繁体文本
     * @param string $variant 源繁体变体:t 通用繁体(默认) / tw 台湾正体 / hk 香港繁体
     * @return string 简体文本
     */
    public static function toSimplified(string $text, string $variant = 't'): string
    {
        self::assertOpenccInstalled();

        return match ($variant) {
            'tw' => \Overtrue\PHPOpenCC\OpenCC::tw2sp($text),
            'hk' => \Overtrue\PHPOpenCC\OpenCC::hk2s($text),
            default => \Overtrue\PHPOpenCC\OpenCC::t2s($text),
        };
    }

    private static function assertOpenccInstalled(): void
    {
        if (!class_exists(\Overtrue\PHPOpenCC\OpenCC::class)) {
            throw new \RuntimeException('缺少 Composer 包：overtrue/php-opencc');
        }
    }
}
