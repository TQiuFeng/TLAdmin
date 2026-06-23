<?php

namespace app\common\router;

use app\adminapi\vo\PageVo;
use app\adminapi\vo\PaginationVo;
use app\common\http\Response;

/**
 * 注解路由扫描器。
 *
 * 反射扫描控制器目录,把 RestController/XxxMapping 注解编译成路由数组
 * [method, path, 'ClassFQN@action', meta],与 Router 和 OpenAPI 生成器直接对接。
 *
 * response 推断规则(可被 Mapping 的 response 参数覆盖):
 *   - 返回 PageVo            → {list: [listOf], pagination: PaginationVo}(分页)
 *   - 返回 array + listOf    → [listOf](VO 列表)
 *   - 返回 array             → 空数组说明
 *   - 返回其他 VO 类         → 该类
 *   - 返回 Response / void   → 不生成(raw 接口用 raw: true 标记)
 *
 * 结果缓存到 runtime/cache/routes.php,按控制器目录最大 mtime 自动失效:
 * 开发期改完注解即生效,生产环境零反射开销。
 * Author: qiufeng
 */
final class RouteScanner
{
    public static function scan(string $controllerDir, string $namespace, ?string $cacheFile = null): array
    {
        // 生产环境信任已生成的路由缓存,跳过 glob + 逐文件 filemtime 的磁盘探测;
        // 改了控制器注解后重建缓存:删除缓存文件,或临时把 APP_DEBUG 置 true。
        if ($cacheFile !== null && is_file($cacheFile)
            && \app\common\config\EnvLoader::get('APP_DEBUG') !== 'true') {
            return (require $cacheFile)['routes'];
        }

        $files = glob(rtrim($controllerDir, '/') . '/*.php') ?: [];

        if ($cacheFile !== null) {
            $maxMtime = max(array_map('filemtime', $files) ?: [0]);
            if (is_file($cacheFile)) {
                $cached = require $cacheFile;
                if (($cached['mtime'] ?? -1) === $maxMtime) {
                    return $cached['routes'];
                }
            }
        }

        $routes = [];
        foreach ($files as $file) {
            $class = rtrim($namespace, '\\') . '\\' . basename($file, '.php');
            if (!class_exists($class)) {
                continue;
            }

            array_push($routes, ...self::scanClass($class));
        }

        if ($cacheFile !== null) {
            self::writeCache($cacheFile, $maxMtime ?? 0, $routes);
        }

        return $routes;
    }

    private static function scanClass(string $class): array
    {
        $reflection = new \ReflectionClass($class);

        $controllerAttrs = $reflection->getAttributes(RestController::class);
        if ($controllerAttrs === []) {
            return [];
        }

        /** @var RestController $controller */
        $controller = $controllerAttrs[0]->newInstance();
        $routes = [];

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($method->getAttributes(Mapping::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                /** @var Mapping $mapping */
                $mapping = $attribute->newInstance();
                $routes[] = [
                    $mapping->httpMethod(),
                    self::resolvePath($controller->prefix, $mapping->path),
                    $class . '@' . $method->getName(),
                    self::buildMeta($controller, $mapping, $method),
                ];
            }
        }

        return $routes;
    }

    private static function resolvePath(string $prefix, string $path): string
    {
        if (str_starts_with($path, '/adminapi')) {
            return $path;
        }

        return rtrim($prefix, '/') . $path;
    }

    private static function buildMeta(RestController $controller, Mapping $mapping, \ReflectionMethod $method): array
    {
        $meta = [
            'tag' => $mapping->tag ?? $controller->tag,
            'summary' => $mapping->summary !== '' ? $mapping->summary : $method->getName(),
            'message' => $mapping->message,
        ];

        if (!$mapping->auth) {
            $meta['auth'] = false;
        }
        if ($mapping->permission !== '') {
            $meta['permission'] = $mapping->permission;
        }
        if ($mapping->raw) {
            $meta['response_raw'] = true;
        }
        if ($mapping->body !== []) {
            $meta['body'] = $mapping->body;
        }
        if ($mapping->query !== []) {
            $meta['query'] = $mapping->query;
        }

        $response = $mapping->response ?? self::inferResponse($mapping, $method);
        if ($response !== null) {
            $meta['response'] = $response;
        }

        return $meta;
    }

    private static function inferResponse(Mapping $mapping, \ReflectionMethod $method): string|array|null
    {
        $type = $method->getReturnType();
        if (!$type instanceof \ReflectionNamedType) {
            return null;
        }

        $name = $type->getName();

        if ($name === PageVo::class) {
            return [
                'list' => [$mapping->listOf ?? 'object 行数据'],
                'pagination' => PaginationVo::class,
            ];
        }

        if ($name === 'array') {
            return $mapping->listOf !== null
                ? [$mapping->listOf]
                : 'array 空数组,无业务数据';
        }

        if (!$type->isBuiltin() && $name !== Response::class) {
            return $name;
        }

        return null;
    }

    private static function writeCache(string $cacheFile, int $mtime, array $routes): void
    {
        $dir = dirname($cacheFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = "<?php\n\n// 注解路由缓存,自动生成请勿手改;删除本文件或修改控制器后自动重建。\nreturn "
            . var_export(['mtime' => $mtime, 'routes' => $routes], true)
            . ";\n";

        file_put_contents($cacheFile, $content, LOCK_EX);
    }
}
