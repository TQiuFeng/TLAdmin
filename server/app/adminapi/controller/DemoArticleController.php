<?php

namespace app\adminapi\controller;

use app\adminapi\vo\CreatedVo;
use app\adminapi\vo\PageVo;
use app\adminapi\vo\DemoArticleVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\DeleteMapping;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\PutMapping;
use app\common\router\RestController;
use app\common\service\gen\DemoArticleService;

/**
 * 演示文章控制器。
 *
 * 只负责读取请求参数、调用 DemoArticleService、返回 VO;不写业务逻辑。
 * 路由、权限标识、OpenAPI 文档均由方法上的注解自动生成。
 * Author: qiufeng(代码生成器)
 */
#[RestController('/adminapi/demo-article', tag: '演示文章')]
final class DemoArticleController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly DemoArticleService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    /** 分页列表:查询参数 page/page_size + 各搜索字段 */

    #[GetMapping(permission: 'demo-article:list', summary: '演示文章列表', listOf: DemoArticleVo::class)]
    public function index(): PageVo
    {
        return PageVo::of($this->service->paginate(
            [
                'title' => (string) $this->query('title', ''),
                'author' => (string) $this->query('author', ''),
                'category' => (string) $this->query('category', ''),
                'is_top' => (string) $this->query('is_top', ''),
                'publish_time_start' => (string) $this->query('publish_time_start', ''),
                'publish_time_end' => (string) $this->query('publish_time_end', ''),
                'status' => (string) $this->query('status', ''),
            ],
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20)))
        ), DemoArticleVo::class);
    }

    /** 详情:路径参数 {id} */
    #[GetMapping('/{id}', permission: 'demo-article:list', summary: '演示文章详情')]
    public function show(): DemoArticleVo
    {
        return DemoArticleVo::from($this->service->detail((int) $this->param('id')));
    }

    /** 新增:请求体为表单字段,返回新记录 ID */
    #[PostMapping(permission: 'demo-article:create', summary: '新增演示文章', message: '创建成功', body: [
        'title' => ['type' => 'string', 'description' => '文章标题'],
        'author' => ['type' => 'string', 'description' => '作者'],
        'category' => ['type' => 'string', 'description' => '分类'],
        'summary' => ['type' => 'string', 'description' => '摘要'],
        'content' => ['type' => 'string', 'description' => '正文'],
        'views' => ['type' => 'integer', 'description' => '阅读量'],
        'is_top' => ['type' => 'integer', 'description' => '置顶'],
        'publish_time' => ['type' => 'integer', 'description' => '发布时间'],
        'status' => ['type' => 'integer', 'description' => '状态'],
    ])]
    public function store(): CreatedVo
    {
        return CreatedVo::from(['id' => $this->service->create($this->request->all())]);
    }

    /** 编辑:路径参数 {id} + 请求体表单字段 */
    #[PutMapping('/{id}', permission: 'demo-article:update', summary: '编辑演示文章', message: '更新成功', body: [
        'title' => ['type' => 'string', 'description' => '文章标题'],
        'author' => ['type' => 'string', 'description' => '作者'],
        'category' => ['type' => 'string', 'description' => '分类'],
        'summary' => ['type' => 'string', 'description' => '摘要'],
        'content' => ['type' => 'string', 'description' => '正文'],
        'views' => ['type' => 'integer', 'description' => '阅读量'],
        'is_top' => ['type' => 'integer', 'description' => '置顶'],
        'publish_time' => ['type' => 'integer', 'description' => '发布时间'],
        'status' => ['type' => 'integer', 'description' => '状态'],
    ])]
    public function update(): array
    {
        $this->service->update((int) $this->param('id'), $this->request->all());

        return [];
    }

    /** 删除:路径参数 {id} */
    #[DeleteMapping('/{id}', permission: 'demo-article:delete', summary: '删除演示文章', message: '删除成功')]
    public function destroy(): array
    {
        $this->service->delete((int) $this->param('id'));

        return [];
    }
}
