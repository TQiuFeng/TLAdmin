<?php

namespace app\adminapi\controller;

use app\adminapi\vo\ToolboxCalendarVo;
use app\adminapi\vo\ToolboxMoneyVo;
use app\adminapi\vo\ToolboxQrcodeVo;
use app\adminapi\vo\ToolboxRegionDetailVo;
use app\adminapi\vo\ToolboxRegionVo;
use app\adminapi\vo\ToolboxSamplesVo;
use app\adminapi\vo\ToolboxTextVo;
use app\common\exception\BizException;
use app\common\router\GetMapping;
use app\common\router\RestController;
use app\common\support\Tools;

/**
 * 演示中心 · 工具箱。
 *
 * 把内置 Tools 工具库的常用能力做成可在线试用的接口:万年历、拼音/繁体/敏感词、
 * 金额大写、行政区划与邮编、二维码、User-Agent 解析。全部读取本地数据,不调用外部接口。
 * 每个接口返回对应的 Toolbox*Vo,接口文档里能看到完整的返回结构。
 * Author: qiufeng
 */
#[RestController('/adminapi/demo-toolbox', tag: '演示工具箱')]
final class DemoToolboxController extends BaseController
{
    /** 敏感词分类中文名 */
    private const SENSITIVE_CATEGORIES = [
        'ad' => '广告', 'porn' => '色情', 'politics' => '政治', 'anti_government' => '反动',
        'corruption' => '贪腐', 'livelihood' => '民生', 'terrorism' => '暴恐', 'weapons' => '涉枪', 'other' => '其他',
        'custom' => '自定义',
    ];

    /** 页面打开时展示的示例 */
    private const SAMPLE_TEXT = '高薪招聘,有意者加QQ';
    private const SAMPLE_AMOUNT = '12345.67';
    private const SAMPLE_QRCODE = 'https://gitee.com/QiuTianLuoYe/tongliao';
    private const SAMPLE_REGION = ['33', '3301', '330106'];

    #[GetMapping('/samples', permission: 'demo-toolbox:use', summary: '页面初始示例:一次返回各卡片的示例结果,避免打开页面就触发接口限流')]
    public function samples(): ToolboxSamplesVo
    {
        $levels = [Tools::regionProvinces()];
        foreach (array_slice(self::SAMPLE_REGION, 0, -1) as $code) {
            $levels[] = Tools::regionChildren($code);
        }

        return ToolboxSamplesVo::from([
            'text' => $this->buildText(self::SAMPLE_TEXT),
            'money' => $this->buildMoney(self::SAMPLE_AMOUNT),
            'qrcode' => $this->buildQrcode(self::SAMPLE_QRCODE),
            'user_agent' => Tools::parseUserAgent($this->request->header('user-agent', '')),
            'region' => [
                'selected' => self::SAMPLE_REGION,
                'levels' => array_map(fn (array $rows): array => ['items' => $this->withLeaf($rows)], $levels),
                'detail' => $this->buildRegionDetail(self::SAMPLE_REGION[count(self::SAMPLE_REGION) - 1]),
            ],
        ]);
    }

    #[GetMapping('/calendar', permission: 'demo-toolbox:use', summary: '万年历:按月返回公历/农历/节气/节假日', query: [
        'month' => ['type' => 'string', 'description' => '月份 YYYY-MM,默认当月', 'example' => '2026-10'],
    ])]
    public function calendar(): ToolboxCalendarVo
    {
        $month = (string) $this->query('month', date('Y-m'));
        if (!preg_match('/^(\d{4})-(\d{1,2})$/', $month, $m) || (int) $m[2] < 1 || (int) $m[2] > 12) {
            throw new BizException('月份格式应为 YYYY-MM');
        }

        return ToolboxCalendarVo::from([
            'month' => sprintf('%04d-%02d', $m[1], $m[2]),
            'today' => Tools::calendar(),
            'next_holiday' => Tools::nextHoliday(),
            'days' => Tools::calendarMonth((int) $m[1], (int) $m[2]),
        ]);
    }

    #[GetMapping('/text', permission: 'demo-toolbox:use', summary: '文字处理:拼音、首字母、URL 别名、繁体、敏感词', query: [
        'text' => ['type' => 'string', 'description' => '待处理文本,最多 200 字', 'required' => true, 'example' => self::SAMPLE_TEXT],
    ])]
    public function text(): ToolboxTextVo
    {
        $raw = (string) $this->query('text', '');
        // 非 UTF-8 字节会让拼音库直接报错,先拦下
        if (!mb_check_encoding($raw, 'UTF-8')) {
            throw new BizException('文本编码必须是 UTF-8');
        }
        $text = mb_substr(trim($raw), 0, 200);
        if ($text === '') {
            throw new BizException('请输入文本');
        }

        return ToolboxTextVo::from($this->buildText($text));
    }

    #[GetMapping('/money', permission: 'demo-toolbox:use', summary: '金额:大写、元分换算', query: [
        'amount' => ['type' => 'string', 'description' => '金额(元)', 'required' => true, 'example' => self::SAMPLE_AMOUNT],
    ])]
    public function money(): ToolboxMoneyVo
    {
        $amount = trim((string) $this->query('amount', ''));
        if (!is_numeric($amount) || (float) $amount < 0 || (float) $amount >= 1e12) {
            throw new BizException('请输入 0 到 1 万亿之间的金额');
        }

        return ToolboxMoneyVo::from($this->buildMoney($amount));
    }

    #[GetMapping('/regions', permission: 'demo-toolbox:use', summary: '行政区划:下级列表(不传 code 返回省份)', listOf: ToolboxRegionVo::class, query: [
        'code' => ['type' => 'string', 'description' => '上级区划编码', 'example' => '33'],
    ])]
    public function regions(): array
    {
        $code = (string) $this->query('code', '');

        return ToolboxRegionVo::list($this->withLeaf($code === '' ? Tools::regionProvinces() : Tools::regionChildren($code)));
    }

    #[GetMapping('/region-detail', permission: 'demo-toolbox:use', summary: '行政区划:完整名称、层级路径、邮编区号', query: [
        'code' => ['type' => 'string', 'description' => '区划编码', 'required' => true, 'example' => '330106'],
    ])]
    public function regionDetail(): ToolboxRegionDetailVo
    {
        return ToolboxRegionDetailVo::from($this->buildRegionDetail((string) $this->query('code', '')));
    }

    #[GetMapping('/qrcode', permission: 'demo-toolbox:use', summary: '生成二维码(base64 图片)', query: [
        'content' => ['type' => 'string', 'description' => '二维码内容,最多 500 字', 'required' => true, 'example' => self::SAMPLE_QRCODE],
    ])]
    public function qrcode(): ToolboxQrcodeVo
    {
        $content = mb_substr((string) $this->query('content', ''), 0, 500);
        if (trim($content) === '') {
            throw new BizException('请输入二维码内容');
        }

        return ToolboxQrcodeVo::from($this->buildQrcode($content));
    }

    // ---------- 结果构建(接口与示例共用) ----------

    private function buildText(string $text): array
    {
        $matches = array_map(
            static fn (array $hit): array => $hit + ['category_label' => self::SENSITIVE_CATEGORIES[$hit['category']] ?? $hit['category']],
            Tools::matchSensitive($text)
        );

        return [
            'text' => $text,
            'pinyin' => Tools::pinyin($text, 'symbol'),
            'pinyin_plain' => Tools::pinyin($text),
            'abbr' => Tools::pinyinAbbr($text),
            'slug' => Tools::pinyinSlug($text),
            'traditional' => Tools::toTraditional($text),
            'sensitive' => [
                'hit' => $matches !== [],
                'matches' => $matches,
                'replaced' => Tools::replaceSensitive($text),
            ],
        ];
    }

    private function buildMoney(string $amount): array
    {
        $fen = Tools::yuanToFen($amount);

        return [
            'amount' => $amount,
            'chinese' => Tools::moneyToCn($amount),
            'fen' => $fen,
            'formatted' => Tools::formatFen($fen),
        ];
    }

    private function buildQrcode(string $content): array
    {
        return ['content' => $content, 'image' => Tools::qrcodeBase64($content, 240, 2)];
    }

    private function buildRegionDetail(string $code): array
    {
        $region = Tools::regionFind($code);
        if ($region === null) {
            throw new BizException('区划编码不存在');
        }

        $path = Tools::regionPath($code);

        // 邮编库只到区县级:从自身往上找第一个完整名称能对上的
        $postcode = null;
        for ($i = count($path) - 1; $i >= 0 && $postcode === null; $i--) {
            $prefix = implode('', array_column(array_slice($path, 0, $i + 1), 'name'));
            foreach (Tools::postcode($path[$i]['name']) as $row) {
                if ($row['full_name'] === $prefix) {
                    $postcode = $row;
                    break;
                }
            }
        }

        return [
            'region' => $region,
            'path' => $path,
            'full_name' => Tools::regionFullName($code),
            'postcode' => $postcode,
        ];
    }

    /** 第 5 级(村/社区)没有下级 */
    private function withLeaf(array $rows): array
    {
        return array_map(static fn (array $row): array => $row + ['leaf' => (int) $row['level'] >= 5], $rows);
    }
}
