<?php

/**
 * TLAdmin 管理端 API 入口。
 *
 * 只做引导:env → 数据库 → 容器(标量参数的 Bean 定义)→ 路由(控制器懒加载)→ 中间件 → 分发。
 * 其余依赖全部由 Container 反射自动装配,新增控制器/服务零改动。
 * Author: qiufeng
 */

declare(strict_types=1);

use app\adminapi\controller\OpenApiController;
use app\adminapi\middleware\AuthMiddleware;
use app\adminapi\middleware\PermissionMiddleware;
use app\adminapi\middleware\RateLimitMiddleware;
use app\common\config\ConfigRepository;
use app\common\config\EnvLoader;
use app\common\container\Container;
use app\common\database\DbBootstrap;
use app\common\http\Request;
use app\common\http\RequestAwareInterface;
use app\common\http\Response;
use app\common\http\Router;
use app\common\response\ApiResponseFactory;
use app\common\router\RouteScanner;
use app\common\service\geo\IpLocationService;
use app\common\service\integration\StorageService;
use app\common\service\log\OperationLogService;
use app\common\service\security\IpBlockService;
use app\common\service\system\AttachmentService;

$rootPath = dirname(__DIR__);
require $rootPath . '/vendor/autoload.php';

EnvLoader::load($rootPath . '/.env');
DbBootstrap::init();

// 出站 HTTP 请求统一落第三方请求日志(MongoDB,w=0 不阻塞)
\app\common\support\Tools::$httpAfterRequest = static function (array $result): void {
    (new \app\common\service\log\ThirdPartyLogService())->record($result);
};

$startedAt = microtime(true);

// 注解路由:扫描控制器上的 RestController/XxxMapping 注解,缓存按目录 mtime 自动失效
$routes = RouteScanner::scan(
    $rootPath . '/app/adminapi/controller',
    'app\\adminapi\\controller',
    $rootPath . '/runtime/cache/routes.php'
);

// ---- 容器:只显式定义构造参数含标量(路径/数组)的 Bean,其余自动装配 ----
$container = new Container();
$container->instance(Container::class, $container);
$container->bind(ConfigRepository::class, static fn (): ConfigRepository => new ConfigRepository(
    [
        $rootPath . '/config/app.php',
        $rootPath . '/config/api.php',
        $rootPath . '/config/integration.php',
        $rootPath . '/config/security.php',
    ],
    $rootPath . '/runtime/config/system.json'
));
$container->bind(IpLocationService::class, static fn (): IpLocationService => new IpLocationService($rootPath . '/resources/geo/ip_ranges.csv'));
$container->bind(AttachmentService::class, static fn (Container $c): AttachmentService => new AttachmentService($c->get(StorageService::class), $rootPath));
$container->bind(\app\common\service\system\GeneratorService::class, static fn (): \app\common\service\system\GeneratorService => new \app\common\service\system\GeneratorService($rootPath, dirname($rootPath) . '/web'));
$container->bind(OpenApiController::class, static fn (Container $c): OpenApiController => new OpenApiController(
    $c->get(ApiResponseFactory::class),
    $c->get(ConfigRepository::class),
    $c->get(\app\common\openapi\ApiDocsAccess::class),
    $routes
));

// ---- 路由注册:控制器懒加载,只实例化命中路由的那一个 ----
$router = $container->get(Router::class);

foreach ($routes as [$method, $path, $handler, $meta]) {
    [$class, $action] = explode('@', $handler);
    $router->add($method, $path, static function (Request $request) use ($container, $class, $action): mixed {
        $controller = $container->get($class);
        if ($controller instanceof RequestAwareInterface) {
            $controller->setRequest($request);
        }

        return $controller->{$action}();
    }, $meta);
}

// ---- 中间件:IP 归属地屏蔽 → 认证 → 限流 → 权限 ----
$router->middleware(static function (Request $request) use ($container): ?Response {
    $result = $container->get(IpBlockService::class)->evaluateRequest($request);

    return $result['blocked']
        ? $container->get(ApiResponseFactory::class)->fail($request, '当前 IP 归属地已被限制访问', 40300, $result, 403)
        : null;
});
$router->middleware($container->get(AuthMiddleware::class));
$router->middleware($container->get(RateLimitMiddleware::class));
$router->middleware($container->get(PermissionMiddleware::class));

// ---- 分发、响应、(连接断开后)异步落操作日志 ----
$request = Request::createFromGlobals();
$response = $router->dispatch($request);

// 先把响应发回客户端;PHP-FPM 下 fastcgi_finish_request() 立即结束请求,
// 操作日志、第三方请求日志都在客户端拿到响应之后才写,不计入接口耗时。
$response->send();
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}

$container->get(OperationLogService::class)->record($request, $response, $startedAt);
