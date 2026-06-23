<?php

namespace app\common\openapi;

use app\common\config\ConfigRepository;
use app\common\config\EnvLoader;
use app\common\http\Request;
use app\common\http\Response;

/**
 * 接口文档(OpenAPI/调试台)访问控制。
 *
 * 两道闸:
 *   1. 仅 APP_DEBUG=true 时开放,否则返回 404(不向生产环境确认文档存在);
 *   2. 开启 Basic Auth(api.docs.auth.enabled)时,账号密码需匹配,否则返回 401 challenge。
 * 命中拦截返回对应 Response,放行返回 null。
 *
 * 注:调试开关直接读 .env(与 RouteScanner 一致),不走 ConfigRepository——
 * runtime/config/system.json 会把保存时的 app.* 固化进去,经它读会被旧值覆盖。
 * Author: qiufeng
 */
final class ApiDocsAccess
{
    public function __construct(private readonly ConfigRepository $config)
    {
    }

    public function deny(Request $request): ?Response
    {
        if (EnvLoader::get('APP_DEBUG') !== 'true') {
            return new Response('Not Found', 404, ['Content-Type' => 'text/plain; charset=utf-8']);
        }

        if (!(bool) $this->config->get('api.docs.auth.enabled', false)) {
            return null;
        }

        $expectedUser = (string) $this->config->get('api.docs.auth.username', '');
        $expectedPass = (string) $this->config->get('api.docs.auth.password', '');
        $credentials = $request->basicAuthCredentials();

        if ($credentials !== null
            && hash_equals($expectedUser, $credentials[0])
            && hash_equals($expectedPass, $credentials[1])
        ) {
            return null;
        }

        return new Response('Unauthorized', 401, [
            'WWW-Authenticate' => 'Basic realm="TLAdmin API Docs", charset="UTF-8"',
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
