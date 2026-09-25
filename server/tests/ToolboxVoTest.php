<?php

/**
 * 工具箱:Tools 返回值能完整映射到 VO(嵌套对象、对象列表、可空字段)。
 * Author: qiufeng
 */

use app\adminapi\vo\ToolboxCalendarDayVo;
use app\adminapi\vo\ToolboxMoneyVo;
use app\adminapi\vo\ToolboxRegionSampleVo;
use app\adminapi\vo\ToolboxTextVo;
use app\common\support\Tools;

return [
    '国庆节:法定假日映射为嵌套 VO' => static function (TestContext $t): void {
        $day = ToolboxCalendarDayVo::from(Tools::calendar('2026-10-01'))->toArray();
        $t->same('2026-10-01', $day['date']);
        $t->same('国庆节', $day['holiday']['name'] ?? null);
        $t->same(true, $day['holiday']['is_rest'] ?? null);
        $t->same(true, $day['is_rest_day']);
        $t->true(is_array($day['lunar']['yi']), '宜应为数组');
    },

    '普通工作日:holiday 为 null' => static function (TestContext $t): void {
        $day = ToolboxCalendarDayVo::from(Tools::calendar('2026-09-24'))->toArray();
        $t->same(null, $day['holiday']);
        $t->same(true, $day['is_workday']);
    },

    '调休上班日标记为工作日' => static function (TestContext $t): void {
        $day = ToolboxCalendarDayVo::from(Tools::calendar('2026-10-10'))->toArray();
        $t->same(false, $day['holiday']['is_rest'] ?? null, '10 月 10 日为国庆调休上班');
        $t->same(true, $day['is_workday']);
    },

    '文字处理:敏感词命中列表映射为 VO 列表' => static function (TestContext $t): void {
        $text = '高薪招聘,有意者加QQ';
        $vo = ToolboxTextVo::from([
            'text' => $text,
            'pinyin' => Tools::pinyin($text, 'symbol'),
            'pinyin_plain' => Tools::pinyin($text),
            'abbr' => Tools::pinyinAbbr($text),
            'slug' => Tools::pinyinSlug($text),
            'traditional' => Tools::toTraditional($text),
            'sensitive' => [
                'hit' => true,
                'matches' => array_map(static fn (array $m): array => $m + ['category_label' => '广告'], Tools::matchSensitive($text)),
                'replaced' => Tools::replaceSensitive($text),
            ],
        ])->toArray();

        $t->same('gao xin zhao pin , you yi zhe jia QQ', $vo['pinyin_plain']);
        $t->same(['招聘', '有意者', 'QQ'], array_column($vo['sensitive']['matches'], 'text'));
        $t->notContains('招聘', $vo['sensitive']['replaced']);
    },

    '金额:元转分与大写' => static function (TestContext $t): void {
        $vo = ToolboxMoneyVo::from([
            'amount' => '12345.67',
            'chinese' => Tools::moneyToCn('12345.67'),
            'fen' => Tools::yuanToFen('12345.67'),
            'formatted' => Tools::formatFen(1234567),
        ])->toArray();
        $t->same(1234567, $vo['fen']);
        $t->same('壹万贰仟叁佰肆拾伍元陆角柒分', $vo['chinese']);
    },

    '示例区划:列表的列表按 items 包一层' => static function (TestContext $t): void {
        if (!extension_loaded('pdo_sqlite')) {
            throw new TestSkipped('缺少 pdo_sqlite 扩展');
        }
        $vo = ToolboxRegionSampleVo::from([
            'selected' => ['33', '3301'],
            'levels' => [['items' => Tools::regionProvinces()], ['items' => Tools::regionChildren('33')]],
            'detail' => [
                'region' => Tools::regionFind('3301'),
                'path' => Tools::regionPath('3301'),
                'full_name' => Tools::regionFullName('3301'),
                'postcode' => null,
            ],
        ])->toArray();
        $t->same('杭州市', $vo['levels'][1]['items'][0]['name'] ?? null);
        $t->same('浙江省杭州市', $vo['detail']['full_name']);
        $t->same(null, $vo['detail']['postcode']);
    },
];
