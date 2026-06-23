<?php

namespace app\adminapi\controller;

use app\common\config\ConfigRepository;
use app\common\http\Response;
use app\common\openapi\ApiDocsAccess;
use app\common\openapi\ApiField;
use app\common\response\ApiResponseFactory;
use app\common\router\GetMapping;
use app\common\router\RestController;

/**
 * OpenAPI 文档接口。
 *
 * 从注解路由(RouteScanner 扫描结果)自动生成 OpenAPI 3.0 文档,路由即文档,
 * 新增接口无需手动维护;输出原始 JSON 供 Swagger UI 调试台加载。
 * Author: qiufeng
 */
#[RestController('/adminapi', tag: '基础')]
final class OpenApiController extends BaseController
{
    /** 已生成的 VO schema,输出到 components/schemas */
    private array $voSchemas = [];

    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly ApiDocsAccess $docsAccess,
        private readonly array $routes = []
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[GetMapping('/openapi.json', summary: 'OpenAPI 文档', auth: false, raw: true, response: 'object OpenAPI 3.0 文档原始 JSON,不包统一响应结构')]
    public function json(): Response
    {
        // 仅调试模式开放,可选 Basic Auth 账号密码保护
        $denied = $this->docsAccess->deny($this->request);
        if ($denied !== null) {
            return $denied;
        }

        $paths = [];

        foreach ($this->routes as [$method, $path, $handler, $meta]) {
            $operation = [
                'summary' => $meta['summary'] ?? $handler,
                'tags' => [$meta['tag'] ?? '默认'],
                'responses' => [
                    '200' => [
                        'description' => 'OK',
                        'content' => [
                            'application/json' => [
                                // response_raw 的接口(如本文档接口)不包统一响应结构
                                'schema' => ($meta['response_raw'] ?? false)
                                    ? $this->dataSchema($meta['response'] ?? null)
                                    : $this->envelopeSchema($this->dataSchema($meta['response'] ?? null)),
                            ],
                        ],
                    ],
                ],
            ];

            if (($meta['auth'] ?? true) !== false) {
                $operation['security'] = [['bearerAuth' => []]];
                $operation['responses']['401'] = ['description' => '未登录或 token 失效'];
            }

            if (($meta['permission'] ?? '') !== '') {
                $operation['description'] = '权限标识:`' . $meta['permission'] . '`';
                $operation['responses']['403'] = ['description' => '无权限'];
            }

            if (preg_match_all('/\{(\w+)\}/', $path, $matches)) {
                foreach ($matches[1] as $param) {
                    $operation['parameters'][] = [
                        'name' => $param,
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'string'],
                    ];
                }
            }

            // 注解 query 声明 → query 参数(让 GET 接口的查询参数能在 Swagger UI 填写)
            foreach ($meta['query'] ?? [] as $field => $def) {
                $operation['parameters'][] = $this->queryParameter((string) $field, $def);
            }

            // 通用响应格式参数
            $operation['parameters'][] = [
                'name' => 'format',
                'in' => 'query',
                'required' => false,
                'description' => '响应格式,默认走系统配置',
                'schema' => ['type' => 'string', 'enum' => ['json', 'xml']],
            ];

            if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
                $bodyDef = $meta['body'] ?? [];
                $operation['requestBody'] = [
                    'required' => $bodyDef !== [],
                    'content' => [
                        'application/json' => [
                            'schema' => $this->requestBodySchema($bodyDef),
                        ],
                    ],
                ];
            }

            $paths[$path][strtolower($method)] = $operation;
        }

        $document = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'TLAdmin API',
                'version' => '1.0.0',
                'description' => "TLAdmin 管理端 API 文档,由路由表自动生成。\n\n统一响应结构:`{code, message, data, request_id, timestamp}`;支持 `format=json|xml` 切换响应格式。",
            ],
            'servers' => [['url' => '/']],
            'paths' => $paths,
            'components' => [
                'schemas' => $this->voSchemas,
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'description' => '登录接口返回的 access_token',
                    ],
                ],
            ],
        ];

        return new Response(
            json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?: '{}',
            200,
            [
                'Content-Type' => 'application/json; charset=utf-8',
                'Access-Control-Allow-Origin' => '*',
            ]
        );
    }

    /**
     * 由注解 query 单条声明生成一个 OpenAPI query 参数。
     * 声明格式与 body 字段一致:详细数组(type/description/required/example)或简单字符串(描述,默认 string)。
     *
     * @param string             $name 参数名
     * @param array<string,mixed>|string $def 字段声明
     * @return array<string,mixed>
     */
    private function queryParameter(string $name, array|string $def): array
    {
        $param = [
            'name' => $name,
            'in' => 'query',
            'required' => false,
        ];

        if (is_array($def)) {
            $schema = ['type' => (string) ($def['type'] ?? 'string')];
            if (array_key_exists('example', $def)) {
                $schema['example'] = $def['example'];
            }
            $param['schema'] = $schema;
            if (isset($def['description'])) {
                $param['description'] = (string) $def['description'];
            }
            $param['required'] = ($def['required'] ?? false) === true;
        } else {
            $param['schema'] = ['type' => 'string'];
            $param['description'] = $def;
        }

        return $param;
    }

    /**
     * 由注解 body 声明生成请求体 schema(让 Swagger UI 显示具体参数)。
     * 未声明时回退为任意对象。
     */
    private function requestBodySchema(array $body): array
    {
        if ($body === []) {
            return ['type' => 'object', 'additionalProperties' => true];
        }

        $properties = [];
        $required = [];
        foreach ($body as $field => $def) {
            $name = (string) $field;
            if (is_array($def)) {
                $schema = ['type' => (string) ($def['type'] ?? 'string')];
                if (isset($def['description'])) {
                    $schema['description'] = (string) $def['description'];
                }
                if (array_key_exists('example', $def)) {
                    $schema['example'] = $def['example'];
                }
                $properties[$name] = $schema;
                if (($def['required'] ?? false) === true) {
                    $required[] = $name;
                }
            } else {
                $properties[$name] = ['type' => 'string', 'description' => (string) $def];
            }
        }

        $schema = ['type' => 'object', 'properties' => $properties];
        if ($required !== []) {
            $schema['required'] = $required;
        }

        return $schema;
    }

    /** 统一响应结构 {code, message, data, request_id, timestamp} 包装 data schema */
    private function envelopeSchema(array $dataSchema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'code' => ['type' => 'integer', 'description' => '业务码,0 表示成功'],
                'message' => ['type' => 'string', 'description' => '提示信息'],
                'data' => $dataSchema,
                'request_id' => ['type' => 'string', 'description' => '请求追踪 ID'],
                'timestamp' => ['type' => 'integer', 'description' => '服务端时间戳(秒)'],
            ],
        ];
    }

    /**
     * 路由 meta 的 response 声明转 OpenAPI schema。
     *
     * 声明格式:
     *   'field' => SomeVo::class         VO 类,反射属性类型和 ApiField 注解生成
     *   'field' => 'int 描述'            标量,类型和描述用空格分隔
     *   'field' => [...]                 关联数组 = 嵌套对象
     *   'field' => [[...]]               单元素列表 = 对象数组
     *   'field' => ['string 描述']       单元素列表 = 标量数组
     */
    private function dataSchema(mixed $definition): array
    {
        if ($definition === null) {
            return ['description' => '业务数据,字段见接口说明'];
        }

        if (is_string($definition) && class_exists($definition)) {
            return $this->voRef($definition);
        }

        if (is_string($definition)) {
            $parts = explode(' ', $definition, 2);
            $schema = $this->scalarType($parts[0]);
            if (isset($parts[1]) && $parts[1] !== '') {
                $schema['description'] = $parts[1];
            }

            return $schema;
        }

        if (is_array($definition) && array_is_list($definition) && count($definition) === 1) {
            return ['type' => 'array', 'items' => $this->dataSchema($definition[0])];
        }

        if (is_array($definition)) {
            $properties = [];
            foreach ($definition as $field => $fieldDefinition) {
                $properties[(string) $field] = $this->dataSchema($fieldDefinition);
            }

            return ['type' => 'object', 'properties' => $properties];
        }

        return ['description' => '业务数据'];
    }

    /** VO 类注册到 components/schemas 并返回 $ref;先占位再生成,支持递归 VO(如菜单树) */
    private function voRef(string $class): array
    {
        $name = substr($class, (int) strrpos($class, '\\') + 1);

        if (!isset($this->voSchemas[$name])) {
            $this->voSchemas[$name] = true;
            $this->voSchemas[$name] = $this->voSchema($class);
        }

        return ['$ref' => "#/components/schemas/{$name}"];
    }

    /** 反射 VO 公开属性(含继承)生成 object schema */
    private function voSchema(string $class): array
    {
        $properties = [];

        foreach ((new \ReflectionClass($class))->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $schema = $this->voPropertySchema($property);

            $attributes = $property->getAttributes(ApiField::class);
            if ($attributes !== []) {
                /** @var ApiField $field */
                $field = $attributes[0]->newInstance();

                if ($field->description !== '') {
                    $schema['description'] = $field->description;
                }
                if ($field->example !== null) {
                    $schema['example'] = $field->example;
                }
                if ($field->listOf !== null) {
                    $schema['type'] = 'array';
                    $schema['items'] = class_exists($field->listOf)
                        ? $this->voRef($field->listOf)
                        : $this->scalarType($field->listOf);
                }
            }

            $properties[$property->getName()] = $schema;
        }

        return ['type' => 'object', 'properties' => $properties];
    }

    private function voPropertySchema(\ReflectionProperty $property): array
    {
        $type = $property->getType();
        if (!$type instanceof \ReflectionNamedType) {
            return [];
        }

        $schema = $type->isBuiltin()
            ? $this->scalarType($type->getName())
            : $this->voRef($type->getName());

        if ($type->allowsNull() && !isset($schema['$ref'])) {
            $schema['nullable'] = true;
        }

        return $schema;
    }

    private function scalarType(string $type): array
    {
        return match ($type) {
            'int', 'integer' => ['type' => 'integer'],
            'float', 'number' => ['type' => 'number'],
            'bool', 'boolean' => ['type' => 'boolean'],
            'string' => ['type' => 'string'],
            'object' => ['type' => 'object', 'additionalProperties' => true],
            'array' => ['type' => 'array', 'items' => []],
            'mixed' => [],
            default => ['description' => $type],
        };
    }
}
