<?php

namespace app\common\support;

/**
 * 工具函数统一入口。
 *
 * 所有公共工具函数都通过 Tools::xxx() 静态调用，只需 use 这一个类。
 * 实现按领域拆分在 tools/ 目录的 trait 中，方法重名时以领域前缀区分（如 arrGet、httpGet）。
 * Author: qiufeng
 */
final class Tools
{
    use tools\DateTools;
    use tools\StrTools;
    use tools\ArrTools;
    use tools\MoneyTools;
    use tools\RandomTools;
    use tools\UuidTools;
    use tools\CryptoTools;
    use tools\FileTools;
    use tools\PinyinTools;
    use tools\HttpTools;
    use tools\ChineseTools;
    use tools\GreetingTools;
    use tools\UserAgentTools;
    use tools\QrcodeTools;
    use tools\MailTools;
    use tools\HolidayTools;
    use tools\SensitiveTools;
    use tools\RegionTools;
    use tools\PostcodeTools;
    use tools\GeoTools;
    use tools\PhoneTools;

    private function __construct()
    {
    }
}
