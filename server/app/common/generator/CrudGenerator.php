<?php

namespace app\common\generator;

use think\facade\Db;

/**
 * CRUD 代码生成器:从数据库表结构 + 字段配置生成后端 Controller/Service/VO、
 * 前端 api/页面、菜单种子,全部贴合 TLAdmin 现有规范(注解路由 + VO + TablePlus)。
 *
 * 字段可配置:每个字段是否进列表(list)、搜索(search + search_type)、表单(form),
 * 以及表单控件类型(component: input/textarea/number/money/switch/datetime)。
 * 搜索方式:like 模糊、eq 精确、between 区间(时间字段,按 字段_start / 字段_end 传秒级时间戳)。
 *
 * CLI 用法:php bin/console gen:crud tl_demo_product --title=演示商品(用默认字段配置)
 * 可视化:后台代码生成器页可逐字段配置并预览代码。
 * Author: qiufeng(代码生成器)
 */
final class CrudGenerator
{
    private string $table;
    private string $entity;
    private string $snake;
    private string $kebab;
    private string $title;
    /** @var array<int, array{name: string, type: string, base: string, comment: string, nullable: bool}> */
    private array $columns = [];
    /** @var array<int, array> 解析并合并配置后的字段 */
    private array $resolved = [];

    /** 可选的表单控件类型 */
    public const COMPONENTS = ['input', 'textarea', 'number', 'money', 'switch', 'datetime'];

    /** 可选的搜索方式 */
    public const SEARCH_TYPES = ['like', 'eq', 'between'];

    public function __construct(
        private readonly string $serverPath,
        private readonly string $webPath
    ) {
    }

    /**
     * 返回字段默认配置(供前端配置界面预填)。
     *
     * @return array<int, array>
     */
    public function defaultColumns(string $table): array
    {
        $this->prepare($table, '');

        return $this->resolveColumns([]);
    }

    /**
     * 构建全部文件内容(纯生成,不写盘),供预览。
     *
     * @param array<string, array> $columnOverrides 按字段名索引的配置覆盖
     * @return array<int, array{root: string, path: string, display: string, language: string, content: string}>
     */
    public function build(string $table, string $title, array $columnOverrides = []): array
    {
        $this->prepare($table, $title);
        $this->resolved = $this->resolveColumns($columnOverrides);

        $defs = [
            ['server', "app/adminapi/vo/{$this->entity}Vo.php", 'php', $this->renderVo()],
            ['server', "app/common/service/gen/{$this->entity}Service.php", 'php', $this->renderService()],
            ['server', "app/adminapi/controller/{$this->entity}Controller.php", 'php', $this->renderController()],
            ['server', "database/seeders/gen_{$this->snake}_menu.php", 'php', $this->renderMenuSeeder()],
            ['web', "src/api/gen/{$this->snake}.ts", 'typescript', $this->renderApi()],
            ['web', "src/pages/gen/{$this->kebab}/index.vue", 'html', $this->renderPage()],
        ];

        $files = [];
        foreach ($defs as [$root, $path, $language, $content]) {
            $files[] = [
                'root' => $root,
                'path' => $path,
                'display' => "{$root}/{$path}",
                'language' => $language,
                'content' => $content,
            ];
        }

        return $files;
    }

    /**
     * 生成并写盘。
     *
     * @param array<string, array> $columnOverrides 字段配置覆盖
     * @return string[] 写入结果说明
     */
    public function generate(string $table, string $title = '', bool $force = false, array $columnOverrides = []): array
    {
        $files = $this->build($table, $title, $columnOverrides);

        $written = [];
        foreach ($files as $file) {
            $base = $file['root'] === 'web' ? $this->webPath : $this->serverPath;
            $ok = $this->write($base . '/' . $file['path'], $file['content'], $force);
            $written[] = $ok ? $file['display'] : "{$file['display']}(已存在,跳过)";
        }

        return $written;
    }

    // ---------- 准备与字段解析 ----------

    private function prepare(string $table, string $title): void
    {
        $this->table = $table;
        $this->snake = preg_replace('/^tl_/', '', $table);
        $this->entity = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->snake)));
        $this->kebab = str_replace('_', '-', $this->snake);
        $this->title = $title !== '' ? $title : $this->entity;
        $this->columns = $this->introspect($table);
    }

    private function introspect(string $table): array
    {
        $rows = Db::query("SHOW FULL COLUMNS FROM `{$table}`");
        if ($rows === []) {
            throw new \RuntimeException("表 {$table} 不存在或没有字段");
        }

        $columns = [];
        foreach ($rows as $row) {
            $type = strtolower((string) $row['Type']);
            // "int unsigned" / "varchar(100)" → "int" / "varchar"
            $base = preg_replace('/[\s(].*/', '', $type);
            $columns[] = [
                'name' => (string) $row['Field'],
                'type' => $type,
                'base' => $base,
                'comment' => (string) ($row['Comment'] ?? ''),
                'nullable' => ($row['Null'] ?? '') === 'YES',
            ];
        }

        return $columns;
    }

    /**
     * 在默认推断基础上叠加用户配置,得到每字段完整配置。
     *
     * @param array<string, array> $overrides
     */
    private function resolveColumns(array $overrides): array
    {
        $resolved = [];
        foreach ($this->columns as $col) {
            $name = $col['name'];
            $isInt = $this->isInt($col);
            $system = in_array($name, ['id', 'create_time', 'update_time', 'delete_time'], true);
            $isText = in_array($col['base'], ['text', 'mediumtext', 'longtext'], true);
            $isTime = in_array($name, ['create_time', 'update_time'], true)
                || str_ends_with($name, '_time') || str_ends_with($name, '_at');

            // 控件推断
            $component = 'input';
            if ($name === 'status') {
                $component = 'switch';
            } elseif ($isTime) {
                $component = 'datetime';
            } elseif ($isText || $name === 'remark') {
                $component = 'textarea';
            } elseif ($isInt && $this->isMoney($col)) {
                // 注释写明"(分)"或字段以 _fen 结尾:库里存分,页面按元显示和输入
                $component = 'money';
            } elseif ($isInt) {
                $component = 'number';
            }

            $def = [
                'name' => $name,
                'base' => $col['base'],
                'comment' => $col['comment'],
                'label' => $this->label($col),
                'php_type' => $isInt ? 'int' : 'string',
                'ts_type' => $isInt ? 'number' : 'string',
                'system' => $system,
                'list' => $name !== 'delete_time',
                'search' => in_array($name, ['name', 'title', 'code', 'username', 'status'], true),
                'search_type' => match (true) {
                    $isTime => 'between',
                    $name === 'status' => 'eq',
                    default => 'like',
                },
                'form' => !$system && $name !== 'delete_time',
                'component' => $component,
            ];

            if (isset($overrides[$name]) && is_array($overrides[$name])) {
                foreach (['label', 'search_type', 'component'] as $strKey) {
                    if (isset($overrides[$name][$strKey]) && $overrides[$name][$strKey] !== '') {
                        $def[$strKey] = (string) $overrides[$name][$strKey];
                    }
                }
                // 不认识的控件 / 搜索方式回退到推断值,避免生成出坏代码
                if (!in_array($def['component'], self::COMPONENTS, true)) {
                    $def['component'] = $component;
                }
                if (!in_array($def['search_type'], self::SEARCH_TYPES, true)) {
                    $def['search_type'] = 'like';
                }
                foreach (['list', 'search', 'form'] as $boolKey) {
                    if (array_key_exists($boolKey, $overrides[$name])) {
                        $def[$boolKey] = (bool) $overrides[$name][$boolKey];
                    }
                }
            }

            // delete_time 永不参与
            if ($name === 'delete_time') {
                $def['list'] = $def['search'] = $def['form'] = false;
            }
            // 主键和时间戳不进表单
            if ($system) {
                $def['form'] = false;
            }
            // 区间搜索只对时间字段有意义
            if ($def['search_type'] === 'between' && $def['component'] !== 'datetime') {
                $def['search_type'] = 'eq';
            }

            $resolved[] = $def;
        }

        return $resolved;
    }

    /**
     * 开关字段的两种取值文案:status 为启用/禁用,其他(如 is_top)为是/否。
     *
     * @return array{0: string, 1: string}
     */
    private function switchLabels(array $col): array
    {
        return $col['name'] === 'status' ? ['启用', '禁用'] : ['是', '否'];
    }

    /** 金额字段:整数列,注释里写明"(分)"或字段名以 _fen 结尾 */
    private function isMoney(array $col): bool
    {
        return preg_match('/[(（]分[)）]/u', $col['comment']) === 1 || str_ends_with($col['name'], '_fen');
    }

    private function isInt(array $col): bool
    {
        return in_array($col['base'], ['int', 'bigint', 'smallint', 'mediumint', 'tinyint'], true);
    }

    private function hasColumn(string $name): bool
    {
        return in_array($name, array_column($this->columns, 'name'), true);
    }

    /** 常见字段的中文标签(无表注释时回退用,避免列标题与字段名/插槽名相同导致 TDesign 误把 title 当插槽) */
    private const FIELD_LABELS = [
        'id' => 'ID', 'create_time' => '创建时间', 'update_time' => '更新时间', 'delete_time' => '删除时间',
        'status' => '状态', 'sort' => '排序', 'remark' => '备注', 'name' => '名称', 'title' => '标题',
        'code' => '编码', 'username' => '账号', 'nickname' => '昵称', 'email' => '邮箱', 'mobile' => '手机号',
        'price' => '价格', 'stock' => '库存', 'avatar' => '头像', 'icon' => '图标',
    ];

    private function label(array $col): string
    {
        if ($col['comment'] !== '') {
            return preg_split('/[,(:：,(]/u', $col['comment'])[0];
        }

        return self::FIELD_LABELS[$col['name']] ?? $col['name'];
    }

    /** @return array<int, array> VO 字段(除 delete_time 外全部) */
    private function voColumns(): array
    {
        return array_values(array_filter($this->resolved, static fn (array $c): bool => $c['name'] !== 'delete_time'));
    }

    private function listColumns(): array
    {
        return array_values(array_filter($this->resolved, static fn (array $c): bool => $c['list']));
    }

    private function formColumns(): array
    {
        return array_values(array_filter($this->resolved, static fn (array $c): bool => $c['form']));
    }

    private function searchColumns(): array
    {
        return array_values(array_filter($this->resolved, static fn (array $c): bool => $c['search']));
    }

    // ---------- 后端模板 ----------

    private function renderVo(): string
    {
        $fields = '';
        foreach ($this->voColumns() as $col) {
            $comment = addslashes($col['comment'] !== '' ? $col['comment'] : $col['name']);
            $fields .= "\n    #[ApiField('{$comment}')]\n    public {$col['php_type']} \${$col['name']};\n";
        }

        return <<<PHP
<?php

namespace app\\adminapi\\vo;

use app\\common\\openapi\\ApiField;

/**
 * {$this->title}行。
 * Author: qiufeng(代码生成器)
 */
final class {$this->entity}Vo extends BaseVo
{{$fields}}

PHP;
    }

    private function renderService(): string
    {
        $softDelete = $this->hasColumn('delete_time');
        $hasTimestamps = $this->hasColumn('create_time') && $this->hasColumn('update_time');
        $whereAlive = $softDelete ? "->whereNull('delete_time')" : '';

        // 列表只查"列表显示"的字段(始终含主键),避免 SELECT * 带出大文本字段
        $listNames = array_column($this->listColumns(), 'name');
        if (!in_array('id', $listNames, true)) {
            array_unshift($listNames, 'id');
        }
        $listFields = implode(', ', $listNames);

        $filters = '';
        foreach ($this->searchColumns() as $col) {
            $name = $col['name'];
            if ($col['search_type'] === 'like') {
                $filters .= <<<PHP

        if ((\$filters['{$name}'] ?? '') !== '') {
            \$query->whereLike('{$name}', '%' . \$filters['{$name}'] . '%');
        }
PHP;
            } elseif ($col['search_type'] === 'between') {
                $filters .= <<<PHP

        if ((\$filters['{$name}_start'] ?? '') !== '') {
            \$query->where('{$name}', '>=', (int) \$filters['{$name}_start']);
        }
        if ((\$filters['{$name}_end'] ?? '') !== '') {
            \$query->where('{$name}', '<=', (int) \$filters['{$name}_end']);
        }
PHP;
            } else {
                $cast = $col['php_type'] === 'int' ? '(int) ' : '';
                $filters .= <<<PHP

        if ((\$filters['{$name}'] ?? '') !== '') {
            \$query->where('{$name}', {$cast}\$filters['{$name}']);
        }
PHP;
            }
        }

        $insertFields = '';
        $updateFields = '';
        foreach ($this->formColumns() as $col) {
            $name = $col['name'];
            $cast = $col['php_type'] === 'int' ? 'int' : 'string';
            $default = $col['php_type'] === 'int' ? '0' : "''";
            $insertFields .= "            '{$name}' => ({$cast}) (\$data['{$name}'] ?? {$default}),\n";
            $updateFields .= <<<PHP
        if (array_key_exists('{$name}', \$data)) {
            \$updates['{$name}'] = ({$cast}) \$data['{$name}'];
        }

PHP;
        }

        $insertTimestamps = $hasTimestamps ? "            'create_time' => \$now,\n            'update_time' => \$now,\n" : '';
        $updateTimestamp = $hasTimestamps ? "            \$updates['update_time'] = time();\n" : '';
        $deleteCode = $softDelete
            ? "// 软删除:置 delete_time,数据保留可恢复\n        Db::table('{$this->table}')->where('id', \$id)->update(['delete_time' => time()]);"
            : "// 物理删除\n        Db::table('{$this->table}')->where('id', \$id)->delete();";

        return <<<PHP
<?php

namespace app\\common\\service\\gen;

use app\\common\\exception\\BizException;
use think\\facade\\Db;

/**
 * {$this->title}业务服务。
 *
 * 负责{$this->title}的分页查询、详情、新增、编辑、删除。
 * 数据访问统一使用 think-orm 查询构造器 Db::table():它把链式调用直接编译为
 * 预处理 SQL 执行,性能与 ORM Model 等价但更轻量,是 TLAdmin 各 Service 的统一写法。
 * 性能要点:列表只查需要展示的字段并按主键倒序分页;搜索命中的字段建议加索引。
 * Author: qiufeng(代码生成器)
 */
final class {$this->entity}Service
{
    /**
     * 分页查询{$this->title}列表。
     *
     * @param array \$filters  过滤条件,键为字段名,空字符串忽略
     * @param int   \$page      页码,从 1 开始
     * @param int   \$pageSize  每页条数
     * @return array{list: array<int, array>, pagination: array{page: int, page_size: int, total: int}}
     */
    public function paginate(array \$filters, int \$page, int \$pageSize): array
    {
        \$query = Db::table('{$this->table}'){$whereAlive};
{$filters}

        // 统计总数:克隆查询,避免被后面的 field/分页影响
        \$total = (clone \$query)->count();

        // 列表仅查需要展示的字段(非 SELECT *),减少 IO 与内存占用
        \$list = \$query
            ->field('{$listFields}')
            ->order('id', 'desc')
            ->page(\$page, \$pageSize)
            ->select()
            ->toArray();

        return [
            'list' => \$list,
            'pagination' => ['page' => \$page, 'page_size' => \$pageSize, 'total' => \$total],
        ];
    }

    /**
     * 按主键查询单条{$this->title}完整记录(用于详情展示和编辑回填)。
     *
     * @throws BizException 记录不存在时抛出 404
     */
    public function detail(int \$id): array
    {
        \$row = Db::table('{$this->table}')->where('id', \$id){$whereAlive}->find();
        if (!\$row) {
            throw BizException::notFound('{$this->title}不存在');
        }

        return \$row;
    }

    /**
     * 新增{$this->title}。
     *
     * 只写入下方显式列出的字段,避免把请求里的多余字段直接落库(字段白名单)。
     *
     * @param array \$data 表单提交的数据
     * @return int 新记录的主键 ID
     */
    public function create(array \$data): int
    {
        \$now = time();

        return (int) Db::table('{$this->table}')->insertGetId([
{$insertFields}{$insertTimestamps}        ]);
    }

    /**
     * 编辑{$this->title}。只更新本次提交且在白名单内的字段。
     *
     * @throws BizException 记录不存在时抛出 404
     */
    public function update(int \$id, array \$data): void
    {
        // 先确认记录存在,不存在直接抛 404
        \$this->detail(\$id);

        \$updates = [];
{$updateFields}
        // 没有任何可更新字段时跳过写库
        if (\$updates !== []) {
{$updateTimestamp}            Db::table('{$this->table}')->where('id', \$id)->update(\$updates);
        }
    }

    /**
     * 删除{$this->title}。
     *
     * @throws BizException 记录不存在时抛出 404
     */
    public function delete(int \$id): void
    {
        // 先确认记录存在
        \$this->detail(\$id);
        {$deleteCode}
    }
}

PHP;
    }

    private function renderController(): string
    {
        $searchParams = '';
        foreach ($this->searchColumns() as $col) {
            $keys = $col['search_type'] === 'between' ? ["{$col['name']}_start", "{$col['name']}_end"] : [$col['name']];
            foreach ($keys as $key) {
                $searchParams .= "                '{$key}' => (string) \$this->query('{$key}', ''),\n";
            }
        }

        // 请求体字段声明(让 Swagger UI 显示具体参数,而非空对象)
        $bodyEntries = '';
        foreach ($this->formColumns() as $col) {
            $type = $col['php_type'] === 'int' ? 'integer' : 'string';
            $label = addslashes($col['label']);
            $bodyEntries .= "        '{$col['name']}' => ['type' => '{$type}', 'description' => '{$label}'],\n";
        }

        return <<<PHP
<?php

namespace app\\adminapi\\controller;

use app\\adminapi\\vo\\CreatedVo;
use app\\adminapi\\vo\\PageVo;
use app\\adminapi\\vo\\{$this->entity}Vo;
use app\\common\\config\\ConfigRepository;
use app\\common\\response\\ApiResponseFactory;
use app\\common\\router\\DeleteMapping;
use app\\common\\router\\GetMapping;
use app\\common\\router\\PostMapping;
use app\\common\\router\\PutMapping;
use app\\common\\router\\RestController;
use app\\common\\service\\gen\\{$this->entity}Service;

/**
 * {$this->title}控制器。
 *
 * 只负责读取请求参数、调用 {$this->entity}Service、返回 VO;不写业务逻辑。
 * 路由、权限标识、OpenAPI 文档均由方法上的注解自动生成。
 * Author: qiufeng(代码生成器)
 */
#[RestController('/adminapi/{$this->kebab}', tag: '{$this->title}')]
final class {$this->entity}Controller extends BaseController
{
    public function __construct(
        ApiResponseFactory \$responseFactory,
        ConfigRepository \$config,
        private readonly {$this->entity}Service \$service
    ) {
        parent::__construct(\$responseFactory, \$config);
    }

    /** 分页列表:查询参数 page/page_size + 各搜索字段 */

    #[GetMapping(permission: '{$this->kebab}:list', summary: '{$this->title}列表', listOf: {$this->entity}Vo::class)]
    public function index(): PageVo
    {
        return PageVo::of(\$this->service->paginate(
            [
{$searchParams}            ],
            max(1, (int) \$this->query('page', 1)),
            min(100, max(1, (int) \$this->query('page_size', 20)))
        ), {$this->entity}Vo::class);
    }

    /** 详情:路径参数 {id} */
    #[GetMapping('/{id}', permission: '{$this->kebab}:list', summary: '{$this->title}详情')]
    public function show(): {$this->entity}Vo
    {
        return {$this->entity}Vo::from(\$this->service->detail((int) \$this->param('id')));
    }

    /** 新增:请求体为表单字段,返回新记录 ID */
    #[PostMapping(permission: '{$this->kebab}:create', summary: '新增{$this->title}', message: '创建成功', body: [
{$bodyEntries}    ])]
    public function store(): CreatedVo
    {
        return CreatedVo::from(['id' => \$this->service->create(\$this->request->all())]);
    }

    /** 编辑:路径参数 {id} + 请求体表单字段 */
    #[PutMapping('/{id}', permission: '{$this->kebab}:update', summary: '编辑{$this->title}', message: '更新成功', body: [
{$bodyEntries}    ])]
    public function update(): array
    {
        \$this->service->update((int) \$this->param('id'), \$this->request->all());

        return [];
    }

    /** 删除:路径参数 {id} */
    #[DeleteMapping('/{id}', permission: '{$this->kebab}:delete', summary: '删除{$this->title}', message: '删除成功')]
    public function destroy(): array
    {
        \$this->service->delete((int) \$this->param('id'));

        return [];
    }
}

PHP;
    }

    private function renderMenuSeeder(): string
    {
        return <<<PHP
<?php

/**
 * {$this->title}菜单和权限节点(代码生成器生成,幂等)。
 * Author: qiufeng(代码生成器)
 */

use think\\facade\\Db;

return [
    'run' => static function (): bool {
        if (Db::table('tl_admin_menu')->where('permission', '{$this->kebab}:list')->find()) {
            return false;
        }

        \$now = time();

        \$menuId = Db::table('tl_admin_menu')->insertGetId([
            'parent_id' => 0, 'type' => 'menu', 'title' => '{$this->title}',
            'name' => '{$this->entity}', 'path' => '/gen/{$this->kebab}', 'component' => 'gen/{$this->kebab}/index',
            'icon' => 'view-module', 'permission' => '{$this->kebab}:list', 'sort' => 90,
            'visible' => 1, 'status' => 1, 'create_time' => \$now, 'update_time' => \$now,
        ]);

        foreach ([['新增', 'create'], ['编辑', 'update'], ['删除', 'delete']] as \$i => [\$title, \$action]) {
            Db::table('tl_admin_menu')->insert([
                'parent_id' => \$menuId, 'type' => 'button', 'title' => \$title,
                'name' => '', 'path' => '', 'component' => '', 'icon' => '',
                'permission' => "{$this->kebab}:{\$action}", 'sort' => \$i,
                'visible' => 1, 'status' => 1, 'create_time' => \$now, 'update_time' => \$now,
            ]);
        }

        return true;
    },
];

PHP;
    }

    // ---------- 前端模板 ----------

    private function renderApi(): string
    {
        $itemFields = '';
        foreach ($this->voColumns() as $col) {
            $itemFields .= "  {$col['name']}: {$col['ts_type']};\n";
        }

        $formFields = '';
        foreach ($this->formColumns() as $col) {
            $formFields .= "  {$col['name']}: {$col['ts_type']};\n";
        }

        return <<<TS
/**
 * {$this->title}接口。
 * Author: qiufeng(代码生成器)
 */
import http from '@/utils/request';
import type { PageResult } from '@/types/api';

/** 列表行(仅含配置为"列表显示"的字段) */
export interface {$this->entity}Item {
{$itemFields}}

/** 新增/编辑表单 */
export interface {$this->entity}Form {
  id?: number;
{$formFields}}

/** 分页查询{$this->title}列表 */
export const list{$this->entity}s = (params: Record<string, unknown>) =>
  http.get<PageResult<{$this->entity}Item>>('/adminapi/{$this->kebab}', params);

/** 查询{$this->title}详情(完整字段,用于编辑回填) */
export const get{$this->entity} = (id: number) => http.get<{$this->entity}Item>(`/adminapi/{$this->kebab}/\${id}`);

/** 新增{$this->title} */
export const create{$this->entity} = (data: {$this->entity}Form) => http.post<{ id: number }>('/adminapi/{$this->kebab}', data);

/** 编辑{$this->title} */
export const update{$this->entity} = (id: number, data: {$this->entity}Form) => http.put(`/adminapi/{$this->kebab}/\${id}`, data);

/** 删除{$this->title} */
export const delete{$this->entity} = (id: number) => http.delete(`/adminapi/{$this->kebab}/\${id}`);

TS;
    }

    private function renderPage(): string
    {
        $needFormatDate = false;
        $needFormatMoney = false;

        // 表格列
        $tableColumns = '';
        foreach ($this->listColumns() as $col) {
            $width = $col['name'] === 'id' ? 70 : 140;
            $tableColumns .= "  { colKey: '{$col['name']}', title: '{$col['label']}', width: {$width} },\n";
        }
        $tableColumns .= "  { colKey: 'op', title: '操作', width: 120, fixed: 'right' as const },";

        // 列自定义槽(开关 → 标签;时间 → 日期格式化)
        $columnSlots = '';
        foreach ($this->listColumns() as $col) {
            $name = $col['name'];
            if ($col['component'] === 'switch') {
                [$on, $off] = $this->switchLabels($col);
                // 状态字段关闭时标红,其他开关(如置顶)关闭只是普通状态,用灰色
                $offTheme = $name === 'status' ? 'danger' : 'default';
                $columnSlots .= <<<HTML

    <template #{$name}="{ row }">
      <t-tag :theme="row.{$name} === 1 ? 'success' : '{$offTheme}'" variant="light">
        {{ row.{$name} === 1 ? '{$on}' : '{$off}' }}
      </t-tag>
    </template>
HTML;
            } elseif ($col['component'] === 'datetime') {
                $needFormatDate = true;
                $columnSlots .= "\n    <template #{$name}=\"{ row }\">{{ formatDate(row.{$name}) }}</template>";
            } elseif ($col['component'] === 'money') {
                $needFormatMoney = true;
                $columnSlots .= "\n    <template #{$name}=\"{ row }\">{{ formatMoney(row.{$name}, { fromFen: true }) }}</template>";
            }
        }

        // 搜索区
        $searchInputs = '';
        foreach ($this->searchColumns() as $col) {
            $name = $col['name'];
            if ($col['component'] === 'switch') {
                [$on, $off] = $this->switchLabels($col);
                $searchInputs .= <<<HTML
      <t-select v-model="query.{$name}" placeholder="{$col['label']}" clearable>
        <t-option label="{$on}" :value="1" />
        <t-option label="{$off}" :value="0" />
      </t-select>

HTML;
            } elseif ($col['search_type'] === 'between') {
                // 日期区间:库里存秒级时间戳;结束日取当天 23:59:59
                $searchInputs .= <<<HTML
      <t-date-range-picker
        :value="query.{$name}_start ? [Number(query.{$name}_start) * 1000, Number(query.{$name}_end) * 1000] : []"
        value-type="time-stamp"
        clearable
        :placeholder="['{$col['label']}起', '{$col['label']}止']"
        @change="(v: unknown) => {
          const [start, end] = (v as number[]) ?? [];
          query.{$name}_start = start ? String(Math.floor(start / 1000)) : '';
          query.{$name}_end = end ? String(Math.floor(end / 1000) + 86399) : '';
        }"
      />

HTML;
            } else {
                $searchInputs .= "      <t-input v-model=\"query.{$name}\" placeholder=\"{$col['label']}\" clearable />\n";
            }
        }
        $searchSection = $searchInputs !== ''
            ? "\n    <template #search>\n{$searchInputs}    </template>\n"
            : '';

        // 表单项
        $formItems = '';
        foreach ($this->formColumns() as $col) {
            $name = $col['name'];
            $label = $col['label'];
            [$on, $off] = $this->switchLabels($col);
            $formItems .= match ($col['component']) {
                'switch' => <<<HTML
      <t-form-item label="{$label}" name="{$name}">
        <t-radio-group v-model="form.{$name}">
          <t-radio :value="1">{$on}</t-radio>
          <t-radio :value="0">{$off}</t-radio>
        </t-radio-group>
      </t-form-item>

HTML,
                // 时间字段库里存秒级时间戳,日期选择器用毫秒,这里做换算
                'datetime' => <<<HTML
      <t-form-item label="{$label}" name="{$name}">
        <t-date-picker
          :value="form.{$name} ? form.{$name} * 1000 : undefined"
          enable-time-picker
          value-type="time-stamp"
          clearable
          placeholder="请选择{$label}"
          @change="(v: unknown) => (form.{$name} = v ? Math.floor(Number(v) / 1000) : 0)"
        />
      </t-form-item>

HTML,
                'number' => <<<HTML
      <t-form-item label="{$label}" name="{$name}">
        <t-input-number v-model="form.{$name}" />
      </t-form-item>

HTML,
                // 金额库里存分,输入框按元显示,改动时换算回分
                'money' => <<<HTML
      <t-form-item label="{$label}" name="{$name}">
        <t-input-number
          :value="form.{$name} / 100"
          :decimal-places="2"
          :min="0"
          theme="normal"
          suffix="元"
          style="width: 200px"
          @change="(v: unknown) => (form.{$name} = Math.round(Number(v || 0) * 100))"
        />
      </t-form-item>

HTML,
                'textarea' => <<<HTML
      <t-form-item label="{$label}" name="{$name}">
        <t-textarea v-model="form.{$name}" placeholder="请输入{$label}" />
      </t-form-item>

HTML,
                default => <<<HTML
      <t-form-item label="{$label}" name="{$name}">
        <t-input v-model="form.{$name}" placeholder="请输入{$label}" />
      </t-form-item>

HTML,
            };
        }

        // 空表单默认值 / 编辑回填
        $emptyFields = '';
        $editAssigns = '';
        foreach ($this->formColumns() as $col) {
            $name = $col['name'];
            $default = match (true) {
                $col['component'] === 'switch' => $name === 'status' ? '1' : '0',
                $col['ts_type'] === 'number' => '0',
                default => "''",
            };
            $emptyFields .= "{$name}: {$default}, ";
            $editAssigns .= "    {$name}: detail.{$name},\n";
        }

        // 搜索条件初值
        $searchQuery = '';
        foreach ($this->searchColumns() as $col) {
            $searchQuery .= match (true) {
                $col['search_type'] === 'between' => "{$col['name']}_start: '', {$col['name']}_end: '', ",
                $col['component'] === 'switch' => "{$col['name']}: '' as string | number, ",
                default => "{$col['name']}: '', ",
            };
        }

        $camelPlural = 'list' . $this->entity . 's';
        $formatDateImport = $needFormatDate ? "\nimport { formatDate } from '@/utils/date';" : '';
        $formatDateImport .= $needFormatMoney ? "\nimport { formatMoney } from '@/utils/money';" : '';

        return <<<VUE
<!--
  {$this->title}管理(代码生成器生成)。
  Author: qiufeng(代码生成器)
-->
<template>
  <table-plus ref="tableRef" :columns="columns" :fetcher="{$camelPlural}" :query="query">
{$searchSection}
    <template #toolbar>
      <t-button v-permission="'{$this->kebab}:create'" theme="primary" @click="openCreate">
        <template #icon><add-icon /></template>新增{$this->title}
      </t-button>
    </template>
{$columnSlots}

    <template #op="{ row }">
      <t-space size="12px">
        <t-link v-permission="'{$this->kebab}:update'" theme="primary" @click="openEdit(row)">编辑</t-link>
        <t-popconfirm content="确定删除吗?" theme="danger" @confirm="onDelete(row)">
          <t-link v-permission="'{$this->kebab}:delete'" theme="danger">删除</t-link>
        </t-popconfirm>
      </t-space>
    </template>
  </table-plus>

  <form-dialog v-model:visible="dialogVisible" :title="form.id ? '编辑{$this->title}' : '新增{$this->title}'" :on-submit="save">
    <t-form ref="formRef" :data="form" label-width="90px">
{$formItems}    </t-form>
  </form-dialog>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import { MessagePlugin, type FormInstanceFunctions } from 'tdesign-vue-next';
import { AddIcon } from 'tdesign-icons-vue-next';
import TablePlus, { type TablePlusExpose } from '@/components/TablePlus.vue';
import FormDialog from '@/components/FormDialog.vue';
import {
  {$camelPlural}, get{$this->entity}, create{$this->entity}, update{$this->entity}, delete{$this->entity},
  type {$this->entity}Item, type {$this->entity}Form,
} from '@/api/gen/{$this->snake}';{$formatDateImport}

const tableRef = ref<TablePlusExpose>();
const query = reactive({ {$searchQuery}});

const columns = [
{$tableColumns}
];

const dialogVisible = ref(false);
const formRef = ref<FormInstanceFunctions>();

const emptyForm = (): {$this->entity}Form => ({ {$emptyFields}});
const form = reactive<{$this->entity}Form>(emptyForm());

function openCreate(): void {
  Object.assign(form, emptyForm(), { id: undefined });
  dialogVisible.value = true;
}

// 列表只返回展示字段,编辑时按 ID 拉取完整详情再回填表单
async function openEdit(row: {$this->entity}Item): Promise<void> {
  const detail = await get{$this->entity}(row.id);
  Object.assign(form, emptyForm(), {
    id: detail.id,
{$editAssigns}  });
  dialogVisible.value = true;
}

async function save(): Promise<void> {
  if (form.id) {
    await update{$this->entity}(form.id, { ...form });
    MessagePlugin.success('更新成功');
  } else {
    await create{$this->entity}({ ...form });
    MessagePlugin.success('创建成功');
  }
  tableRef.value?.refresh();
}

async function onDelete(row: {$this->entity}Item): Promise<void> {
  await delete{$this->entity}(row.id);
  MessagePlugin.success('删除成功');
  tableRef.value?.refresh();
}
</script>

VUE;
    }

    // ---------- 写文件 ----------

    private function write(string $path, string $content, bool $force): bool
    {
        if (is_file($path) && !$force) {
            return false;
        }
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        file_put_contents($path, $content);

        return true;
    }
}
