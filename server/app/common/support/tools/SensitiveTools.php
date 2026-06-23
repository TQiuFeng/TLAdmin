<?php

namespace app\common\support\tools;

/**
 * 敏感词检测工具。
 *
 * DFA 字典树实现，词库本地化存放在 support/data/sensitive/ 下（按分类一行一词），
 * 含反动、涉政、暴恐、涉枪涉爆、色情、广告、贪腐等分类，离线可用。
 * 支持运行时追加词、加载自定义词库文件，检测时可忽略干扰符号。
 * Author: qiufeng
 */
trait SensitiveTools
{
    private static ?array $sensitiveTrie = null;

    private static string $sensitiveSkipChars = " \t\r\n*-_=+~!@#$%^&()[]{}|\\/<>,.?;:'\"`，。！？；：“”‘’、·…—";

    /**
     * 文本是否包含敏感词。
     *
     * @param string $text       待检测文本
     * @param array  $categories 限定的分类列表;为空检测全部分类
     * @return bool 命中任一敏感词返回 true
     */
    public static function isSensitive(string $text, array $categories = []): bool
    {
        return self::matchSensitive($text, $categories, true) !== [];
    }

    /**
     * 找出文本中的敏感词。
     *
     * @param string $text       待检测文本
     * @param array  $categories 限定的分类列表;为空检测全部分类
     * @param bool   $first      为 true 时命中第一个即返回
     * @return array 命中项列表,每项含 word(规范词)、text(原文片段)、category、offset(字符偏移)
     */
    public static function matchSensitive(string $text, array $categories = [], bool $first = false): array
    {
        $trie = self::sensitiveTrie();
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $length = count($chars);
        $skip = preg_split('//u', self::$sensitiveSkipChars, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $skipMap = array_fill_keys($skip, true);

        $matches = [];
        $found = [];

        for ($i = 0; $i < $length; $i++) {
            $node = $trie;
            $word = '';
            $hit = null;

            for ($j = $i; $j < $length; $j++) {
                $char = $chars[$j];
                if ($word !== '' && isset($skipMap[$char])) {
                    continue; // 跳过词中间的干扰符号
                }
                $lower = mb_strtolower($char);
                if (!isset($node[$lower])) {
                    break;
                }
                $node = $node[$lower];
                $word .= $char;
                if (isset($node['#'])) {
                    // 贪婪匹配：记录后继续找更长的词；text 为原文片段（可能含干扰符）
                    $hit = [
                        'word' => $word,
                        'text' => implode('', array_slice($chars, $i, $j - $i + 1)),
                        'category' => $node['#'],
                        'offset' => $i,
                    ];
                }
            }

            if ($hit === null) {
                continue;
            }
            if ($categories !== [] && !in_array($hit['category'], $categories, true)) {
                continue;
            }
            if (!isset($found[$hit['word']])) {
                $found[$hit['word']] = true;
                $matches[] = $hit;
                if ($first) {
                    return $matches;
                }
            }
        }

        return $matches;
    }

    /**
     * 将文本中的敏感词替换为指定字符(按词长重复)。
     *
     * @param string $text        原文本
     * @param string $replacement 替换字符,默认 *
     * @param array  $categories  限定的分类列表;为空检测全部分类
     * @return string 替换后的文本
     */
    public static function replaceSensitive(string $text, string $replacement = '*', array $categories = []): string
    {
        $fragments = array_unique(array_column(self::matchSensitive($text, $categories), 'text'));
        usort($fragments, static fn (string $a, string $b): int => mb_strlen($b) <=> mb_strlen($a));

        foreach ($fragments as $fragment) {
            $text = str_replace($fragment, str_repeat($replacement, mb_strlen($fragment)), $text);
        }

        return $text;
    }

    /**
     * 运行时追加敏感词(仅当前进程内有效)。
     *
     * @param array  $words    要追加的敏感词数组
     * @param string $category 归入的分类,默认 custom
     */
    public static function addSensitiveWords(array $words, string $category = 'custom'): void
    {
        $trie = self::sensitiveTrie();
        foreach ($words as $word) {
            self::sensitiveTrieInsert($trie, (string) $word, $category);
        }
        self::$sensitiveTrie = $trie;
    }

    /**
     * 加载自定义词库文件(一行一词),与内置词库叠加。
     *
     * @param string $file     词库文件路径
     * @param string $category 归入的分类,默认 custom
     * @return int 加载的词条数
     * @throws \InvalidArgumentException 文件不存在时
     */
    public static function loadSensitiveLexicon(string $file, string $category = 'custom'): int
    {
        if (!is_file($file)) {
            throw new \InvalidArgumentException("词库文件不存在：{$file}");
        }

        $words = array_filter(array_map('trim', file($file) ?: []));
        self::addSensitiveWords($words, $category);

        return count($words);
    }

    /**
     * 内置词库分类列表。
     *
     * @return array 分类名数组(对应 data/sensitive 下各词库文件名)
     */
    public static function sensitiveCategories(): array
    {
        $files = glob(self::sensitiveDataDir() . '/*.txt') ?: [];

        return array_map(static fn (string $file): string => basename($file, '.txt'), $files);
    }

    private static function sensitiveTrie(): array
    {
        if (self::$sensitiveTrie !== null) {
            return self::$sensitiveTrie;
        }

        $trie = [];
        foreach (glob(self::sensitiveDataDir() . '/*.txt') ?: [] as $file) {
            $category = basename($file, '.txt');
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $word) {
                self::sensitiveTrieInsert($trie, trim($word), $category);
            }
        }

        return self::$sensitiveTrie = $trie;
    }

    private static function sensitiveTrieInsert(array &$trie, string $word, string $category): void
    {
        if ($word === '') {
            return;
        }

        $node = &$trie;
        foreach (preg_split('//u', mb_strtolower($word), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $char) {
            $node[$char] ??= [];
            $node = &$node[$char];
        }
        $node['#'] = $category;
    }

    private static function sensitiveDataDir(): string
    {
        return __DIR__ . '/../data/sensitive';
    }
}
