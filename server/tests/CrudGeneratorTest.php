<?php

/**
 * 代码生成器:只生成内容(build)不写盘,检查控件推断与生成代码。依赖演示表(执行过 migrate)。
 * Author: qiufeng
 */

use app\common\generator\CrudGenerator;

$generator = static function (): CrudGenerator {
    $root = dirname(__DIR__);

    return new CrudGenerator($root, dirname($root) . '/web');
};

/** 按生成文件的相对路径取内容 */
$file = static function (array $files, string $suffix): string {
    foreach ($files as $item) {
        if (str_ends_with($item['path'], $suffix)) {
            return $item['content'];
        }
    }
    throw new AssertionFailed("没有生成 {$suffix}");
};

return [
    '金额:注释写(分)自动用金额控件' => static function (TestContext $t) use ($generator, $file): void {
        $t->needsDb();
        $columns = array_column($generator()->defaultColumns('tl_demo_product'), 'component', 'name');
        $t->same('money', $columns['price'] ?? null);

        $page = $file($generator()->build('tl_demo_product', '演示商品'), 'index.vue');
        $t->contains('formatMoney(row.price, { fromFen: true })', $page, '列表按元显示');
        $t->contains('form.price = Math.round(Number(v || 0) * 100)', $page, '表单换算回分');
        $t->contains("import { formatMoney } from '@/utils/money';", $page);
    },

    '开关:status 为启用/禁用,其他为是/否且默认否' => static function (TestContext $t) use ($generator, $file): void {
        $t->needsDb();
        $page = $file($generator()->build('tl_demo_article', '演示文章', [
            'is_top' => ['component' => 'switch', 'search' => true, 'search_type' => 'eq'],
        ]), 'index.vue');
        $t->contains("row.is_top === 1 ? '是' : '否'", $page);
        $t->contains("row.status === 1 ? '启用' : '禁用'", $page);
        $t->contains('is_top: 0,', $page, '新增时置顶默认否');
        $t->contains('status: 1,', $page, '新增时状态默认启用');
    },

    '时间:表单用日期选择器,搜索用日期区间' => static function (TestContext $t) use ($generator, $file): void {
        $t->needsDb();
        $files = $generator()->build('tl_demo_article', '演示文章', ['publish_time' => ['search' => true]]);
        $page = $file($files, 'index.vue');
        $t->contains('<t-date-picker', $page);
        $t->contains('<t-date-range-picker', $page);
        $t->contains('publish_time_start: \'\', publish_time_end: \'\',', $page);

        $service = $file($files, 'DemoArticleService.php');
        $t->contains("\$query->where('publish_time', '>=', (int) \$filters['publish_time_start']);", $service);
        $t->contains("\$query->where('publish_time', '<=', (int) \$filters['publish_time_end']);", $service);

        $controller = $file($files, 'DemoArticleController.php');
        $t->contains("'publish_time_end' => (string) \$this->query('publish_time_end', ''),", $controller);
    },

    '不认识的控件和搜索方式回退,区间只用于时间字段' => static function (TestContext $t) use ($generator): void {
        $t->needsDb();
        $columns = [];
        foreach ($generator()->defaultColumns('tl_demo_article') as $col) {
            $columns[$col['name']] = $col;
        }
        $t->same('between', $columns['publish_time']['search_type'], '时间字段默认区间搜索');

        $build = $generator()->build('tl_demo_article', '演示文章', [
            'title' => ['component' => 'rich-editor', 'search' => true, 'search_type' => 'between'],
            'views' => ['search_type' => 'fuzzy'],
        ]);
        $service = '';
        foreach ($build as $item) {
            if (str_ends_with($item['path'], 'Service.php')) {
                $service = $item['content'];
            }
        }
        $t->notContains("title_start", $service, '非时间字段不能用区间');
        $t->contains("\$query->where('title', \$filters['title']);", $service, '回退为精确');
    },
];
