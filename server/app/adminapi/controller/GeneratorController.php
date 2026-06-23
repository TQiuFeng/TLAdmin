<?php

namespace app\adminapi\controller;

use app\adminapi\vo\GenTableVo;
use app\common\config\ConfigRepository;
use app\common\exception\BizException;
use app\common\response\ApiResponseFactory;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\RestController;
use app\common\service\system\GeneratorService;

/**
 * 代码生成器控制器:列出数据库表,按表生成 CRUD 全套代码。
 * Author: qiufeng
 */
#[RestController('/adminapi/generator', tag: '代码生成器')]
final class GeneratorController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly GeneratorService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[GetMapping('/tables', permission: 'system:generator:use', summary: '可生成代码的数据库表', listOf: GenTableVo::class)]
    public function tables(): array
    {
        return GenTableVo::list($this->service->tables());
    }

    #[GetMapping('/columns', permission: 'system:generator:use', summary: '表字段默认配置(列表/搜索/表单/控件)')]
    public function columns(): array
    {
        $table = trim((string) $this->query('table', ''));
        if ($table === '') {
            throw BizException::paramError('请选择要生成的表');
        }

        try {
            return $this->service->columns($table);
        } catch (\InvalidArgumentException $exception) {
            throw BizException::paramError($exception->getMessage());
        } catch (\Throwable $exception) {
            throw BizException::paramError('读取字段失败:' . $exception->getMessage());
        }
    }

    #[PostMapping('/preview', permission: 'system:generator:use', summary: '按字段配置预览生成的代码', auth: true)]
    public function preview(): array
    {
        [$table, $title, $columns] = $this->parseInput();

        try {
            return $this->service->preview($table, $title, $columns);
        } catch (\InvalidArgumentException $exception) {
            throw BizException::paramError($exception->getMessage());
        } catch (\Throwable $exception) {
            throw BizException::paramError('预览失败:' . $exception->getMessage());
        }
    }

    #[PostMapping('/run', permission: 'system:generator:use', summary: '按字段配置生成 CRUD 代码', message: '生成完成')]
    public function run(): array
    {
        [$table, $title, $columns] = $this->parseInput();
        $force = (bool) $this->input('force', false);

        try {
            return $this->service->generate($table, $title, $columns, $force);
        } catch (\InvalidArgumentException $exception) {
            throw BizException::paramError($exception->getMessage());
        } catch (\Throwable $exception) {
            throw BizException::paramError('生成失败:' . $exception->getMessage());
        }
    }

    /** @return array{0: string, 1: string, 2: array} [table, title, columns] */
    private function parseInput(): array
    {
        $table = trim((string) $this->input('table', ''));
        $title = trim((string) $this->input('title', ''));
        $columns = $this->input('columns', []);

        if ($table === '') {
            throw BizException::paramError('请选择要生成的表');
        }

        return [$table, $title, is_array($columns) ? $columns : []];
    }
}
