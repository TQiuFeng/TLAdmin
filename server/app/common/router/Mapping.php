<?php

namespace app\common\router;

/**
 * 方法路由注解基类,类比 Spring 的 @RequestMapping 系列。
 *
 * 路径规则:相对路径拼在 RestController prefix 后;以 /adminapi 开头视为绝对路径。
 * response 自动从方法返回类型推断(VO 类 / PageVo+listOf / array),特殊情况用 response 覆盖。
 *
 * 参数声明(body / query)统一支持两种写法,用于生成 OpenAPI 文档,让 Swagger UI 显示具体参数:
 *   - 简单:['username' => '账号说明']                       字段类型默认 string,值为描述
 *   - 详细:['enabled' => ['type' => 'boolean', 'description' => '开关',
 *            'required' => true, 'example' => true]]         可指定类型/必填/示例
 * body 对应请求体(POST/PUT/PATCH),query 对应 URL 查询参数(GET 等)——两者按 HTTP 方法二选一。
 * Author: qiufeng
 */
abstract class Mapping
{
    /**
     * @param string            $path       路由路径。相对路径拼在所属 RestController 的 prefix 后;
     *                                       以 /adminapi 开头则视为绝对路径,不再拼接 prefix。
     *                                       路径中的 {id} 形式占位符会自动生成为 OpenAPI path 参数。
     * @param string            $permission 访问所需权限标识(如 'system:user:list')。空串表示登录即可,
     *                                       不做按钮级权限校验;非空时由 PermissionMiddleware 拦截校验。
     * @param string            $summary    接口简述,显示在 Swagger UI 标题处;留空则回退为方法名。
     * @param string            $message    业务成功时统一响应里的 message 文案(默认 'ok')。
     * @param bool              $auth       是否需要登录鉴权(默认 true)。设为 false 时跳过 AuthMiddleware,
     *                                       用于登录、健康检查、openapi.json 等公开接口。
     * @param string|null       $listOf     列表元素类型,仅当返回 PageVo 或 array 列表时用于推断 items 类型;
     *                                       可填 VO 类名(生成 $ref)或标量类型名(如 'int 用户ID')。
     * @param string|array|null $response   显式覆盖响应数据 schema,优先级高于按返回类型自动推断。
     *                                       支持 VO 类名 / 'int 描述' 标量 / 嵌套数组 / [单元素]列表,详见
     *                                       OpenApiController::dataSchema 的声明格式说明。
     * @param bool              $raw        是否为原始响应(默认 false)。true 时响应不包统一 envelope 结构,
     *                                       直接输出 data 本身(如导出文件、openapi.json 原始 JSON)。
     * @param string|null       $tag        OpenAPI 分组标签;留空则继承所属 RestController 的 tag。
     * @param array             $body       请求体字段声明(POST/PUT/PATCH 生成 requestBody)。写法见类注释。
     * @param array             $query      URL 查询参数声明(GET 等生成 query parameters)。写法同 body,
     *                                       让 GET 接口的查询参数能在 Swagger UI 里填写发送。
     */
    public function __construct(
        public readonly string $path = '',
        public readonly string $permission = '',
        public readonly string $summary = '',
        public readonly string $message = 'ok',
        public readonly bool $auth = true,
        public readonly ?string $listOf = null,
        public readonly string|array|null $response = null,
        public readonly bool $raw = false,
        public readonly ?string $tag = null,
        public readonly array $body = [],
        public readonly array $query = []
    ) {
    }

    abstract public function httpMethod(): string;
}
