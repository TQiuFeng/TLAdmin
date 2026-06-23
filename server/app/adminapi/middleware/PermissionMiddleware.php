<?php

namespace app\adminapi\middleware;

use app\common\http\Request;
use app\common\http\Response;
use app\common\response\ApiResponseFactory;
use app\common\service\auth\AuthService;

/**
 * 权限校验中间件。
 *
 * 路由 meta 中声明 'permission' => 'system:user:list' 即校验;超级管理员绕过。
 * Author: qiufeng
 */
final class PermissionMiddleware
{
    public function __construct(
        private readonly ApiResponseFactory $responseFactory,
        private readonly AuthService $authService
    ) {
    }

    public function __invoke(Request $request, array $meta): ?Response
    {
        $permission = $meta['permission'] ?? '';
        if ($permission === '' || $request->authUserId() === 0) {
            return null;
        }

        if (!$this->authService->hasPermission($request->authUserId(), $permission)) {
            return $this->responseFactory->fail(
                $request,
                '无权限执行此操作',
                40300,
                ['permission' => $permission],
                403
            );
        }

        return null;
    }
}
