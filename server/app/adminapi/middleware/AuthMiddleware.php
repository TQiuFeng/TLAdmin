<?php

namespace app\adminapi\middleware;

use app\common\http\Request;
use app\common\http\Response;
use app\common\response\ApiResponseFactory;
use app\common\service\auth\TokenService;
use think\facade\Db;

/**
 * 认证中间件。
 *
 * 路由默认需要登录;meta 设置 'auth' => false 的路由(登录、健康检查等)跳过。
 * 校验 Bearer access token 并把用户 ID 写入 Request。
 * Author: qiufeng
 */
final class AuthMiddleware
{
    public function __construct(
        private readonly ApiResponseFactory $responseFactory,
        private readonly TokenService $tokenService
    ) {
    }

    public function __invoke(Request $request, array $meta): ?Response
    {
        if (($meta['auth'] ?? true) === false) {
            return null;
        }

        $token = $request->bearerToken();
        if ($token === '') {
            return $this->responseFactory->fail($request, '未登录,请先登录', 40100, [], 401);
        }

        $userId = $this->tokenService->verifyAccess($token);
        if ($userId === null) {
            return $this->responseFactory->fail($request, '登录已过期,请重新登录', 40100, [], 401);
        }

        $request->setAuthUserId($userId);
        $request->setAuthUserResolver(static function () use ($userId): array {
            $user = Db::table('tl_admin_user')->where('id', $userId)->find();
            if (!$user) {
                return [];
            }

            unset($user['password']);

            return $user;
        });

        return null;
    }
}
