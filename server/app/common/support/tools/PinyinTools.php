<?php

namespace app\common\support\tools;

/**
 * 中文转拼音工具。
 *
 * 基于 overtrue/pinyin，支持无声调、数字声调、声调符号、首字母、slug、姓名和多音字。
 * Author: qiufeng
 */
trait PinyinTools
{
    /**
     * 中文转拼音字符串。
     *
     * @param string $text      中文文本
     * @param string $tone      声调风格:none 无声调 / number 数字声调 / symbol 声调符号,默认 none
     * @param string $separator 拼音之间的连接符,默认空格
     * @return string 拼音字符串
     */
    public static function pinyin(string $text, string $tone = 'none', string $separator = ' '): string
    {
        self::assertPinyinInstalled();

        return \Overtrue\Pinyin\Pinyin::sentence($text, self::pinyinTone($tone))->join($separator);
    }

    /**
     * 转拼音并返回数组,每个字/词一项。
     *
     * @param string $text 中文文本
     * @param string $tone 声调风格 none/number/symbol,默认 none
     * @return array 拼音数组
     */
    public static function pinyinWords(string $text, string $tone = 'none'): array
    {
        self::assertPinyinInstalled();

        return \Overtrue\Pinyin\Pinyin::sentence($text, self::pinyinTone($tone))->toArray();
    }

    /**
     * 转用于 URL 的拼音 slug,如「你好世界」→ ni-hao-shi-jie。
     *
     * @param string $text      中文文本
     * @param string $separator 连接符,默认 -
     * @return string 适合做 URL 的拼音 slug
     */
    public static function pinyinSlug(string $text, string $separator = '-'): string
    {
        self::assertPinyinInstalled();

        return \Overtrue\Pinyin\Pinyin::permalink($text, $separator);
    }

    /**
     * 取每字拼音首字母,如「你好」→ nh。
     *
     * @param string $text      中文文本
     * @param string $separator 首字母之间的连接符,默认空串
     * @return string 拼音首字母缩写
     */
    public static function pinyinAbbr(string $text, string $separator = ''): string
    {
        self::assertPinyinInstalled();

        return \Overtrue\Pinyin\Pinyin::abbr($text)->join($separator);
    }

    /**
     * 人名转拼音,姓氏按习惯读音处理(如「单」读 shàn)。
     *
     * @param string $name      姓名
     * @param string $tone      声调风格 none/number/symbol,默认 none
     * @param string $separator 连接符,默认空格
     * @return string 姓名拼音
     */
    public static function pinyinName(string $name, string $tone = 'none', string $separator = ' '): string
    {
        self::assertPinyinInstalled();

        return \Overtrue\Pinyin\Pinyin::name($name, self::pinyinTone($tone))->join($separator);
    }

    /**
     * 护照式姓名拼音(姓在前、无声调,符合证件填写规范)。
     *
     * @param string $name      姓名
     * @param string $separator 姓与名的连接符,默认空格
     * @return string 护照式姓名拼音
     */
    public static function pinyinPassportName(string $name, string $separator = ' '): string
    {
        self::assertPinyinInstalled();

        return \Overtrue\Pinyin\Pinyin::passportName($name)->join($separator);
    }

    /**
     * 返回多音字的全部读音候选。
     *
     * @param string $text 中文文本
     * @param string $tone 声调风格 none/number/symbol,默认 none
     * @return array 每字一个候选读音数组
     */
    public static function pinyinHeteronym(string $text, string $tone = 'none'): array
    {
        self::assertPinyinInstalled();

        return \Overtrue\Pinyin\Pinyin::heteronym($text, self::pinyinTone($tone))->toArray();
    }

    private static function pinyinTone(string $tone): string
    {
        return match ($tone) {
            'symbol' => 'symbol',
            'number' => 'number',
            default => 'none',
        };
    }

    private static function assertPinyinInstalled(): void
    {
        if (!class_exists(\Overtrue\Pinyin\Pinyin::class)) {
            throw new \RuntimeException('缺少 Composer 包：overtrue/pinyin');
        }
    }
}
