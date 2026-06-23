<?php

namespace app\common\support\tools;

/**
 * UUID 工具。
 *
 * 优先使用 ramsey/uuid 生成标准 UUID v4；缺少依赖时使用 RFC 4122 兼容兜底实现。
 * Author: qiufeng
 */
trait UuidTools
{
    /**
     * 生成标准 UUID v4。
     *
     * @return string 带连字符的 36 位 UUID
     */
    public static function uuid(): string
    {
        if (class_exists(\Ramsey\Uuid\Uuid::class)) {
            return \Ramsey\Uuid\Uuid::uuid4()->toString();
        }

        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * 去掉连字符的 32 位 UUID。
     *
     * @return string 无连字符的 32 位 UUID
     */
    public static function uuidCompact(): string
    {
        return str_replace('-', '', self::uuid());
    }

    /**
     * 生成定长随机短 ID(非标准 UUID,不保证全局唯一)。
     *
     * @param int $length 长度,默认 16
     * @return string 随机短 ID
     */
    public static function uuidShort(int $length = 16): string
    {
        return substr(self::randomString(max($length, 1)), 0, $length);
    }
}
