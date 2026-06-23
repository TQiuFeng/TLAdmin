<?php

namespace app\common\support\tools;

/**
 * 二维码生成工具。
 *
 * 基于 bacon/bacon-qr-code + GD 本地生成，无需网络。
 * 支持 base64（可直接塞 img src）、临时图片文件、二进制 buffer 三种输出。
 * Author: qiufeng
 */
trait QrcodeTools
{
    /**
     * 生成二维码 PNG 二进制内容。
     *
     * @param string $content 二维码内容(文本/URL 等)
     * @param int    $size    图片边长(像素),默认 300
     * @param int    $margin  四周留白(模块数),默认 4
     * @return string PNG 二进制数据
     */
    public static function qrcodeBuffer(string $content, int $size = 300, int $margin = 4): string
    {
        if (!class_exists(\BaconQrCode\Writer::class)) {
            throw new \RuntimeException('缺少 Composer 包：bacon/bacon-qr-code');
        }
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('二维码生成需要 PHP GD 扩展');
        }

        $renderer = new \BaconQrCode\Renderer\GDLibRenderer($size, $margin);

        return (new \BaconQrCode\Writer($renderer))->writeString($content);
    }

    /**
     * 生成二维码并返回 base64 数据,可直接用于 <img src="...">。
     *
     * @param string $content 二维码内容
     * @param int    $size    图片边长(像素),默认 300
     * @param int    $margin  四周留白(模块数),默认 4
     * @return string data:image/png;base64,... 形式的字符串
     */
    public static function qrcodeBase64(string $content, int $size = 300, int $margin = 4): string
    {
        return 'data:image/png;base64,' . base64_encode(self::qrcodeBuffer($content, $size, $margin));
    }

    /**
     * 生成二维码图片文件。
     *
     * @param string      $content 二维码内容
     * @param string|null $path    输出文件路径;传 null 写入系统临时目录、随机文件名
     * @param int         $size    图片边长(像素),默认 300
     * @param int         $margin  四周留白(模块数),默认 4
     * @return string 生成的文件绝对路径
     */
    public static function qrcodeFile(string $content, ?string $path = null, int $size = 300, int $margin = 4): string
    {
        if ($path === null) {
            $dir = sys_get_temp_dir() . '/tladmin-qrcode';
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \RuntimeException("创建二维码临时目录失败：{$dir}");
            }
            $path = $dir . '/' . bin2hex(random_bytes(8)) . '.png';
        }

        if (file_put_contents($path, self::qrcodeBuffer($content, $size, $margin)) === false) {
            throw new \RuntimeException("二维码写入文件失败：{$path}");
        }

        return $path;
    }
}
