<?php

namespace app\adminapi\controller;

use app\common\config\ConfigRepository;
use app\common\http\Request;
use app\common\http\RequestAwareInterface;
use app\common\http\Response;
use app\common\response\ApiResponseFactory;

/**
 * 管理端控制器基类。
 *
 * Router 分发前注入当前 Request,方法无需声明 Request 参数,
 * 直接使用 $this->input()/query()/param()/authUserId() 取请求数据,
 * 完整 Request 对象用 $this->request。
 * Author: qiufeng
 */
abstract class BaseController implements RequestAwareInterface
{
    protected Request $request;

    public function __construct(
        protected readonly ApiResponseFactory $responseFactory,
        protected readonly ConfigRepository $config
    ) {
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /** 请求体/查询参数 */
    protected function input(string $key, mixed $default = null): mixed
    {
        return $this->request->input($key, $default);
    }

    /** 查询参数 */
    protected function query(string $key, mixed $default = null): mixed
    {
        return $this->request->query($key, $default);
    }

    /** 路由路径参数,如 /users/{id} 中的 id */
    protected function param(string $key, mixed $default = null): mixed
    {
        return $this->request->param($key, $default);
    }

    /** 当前登录用户 ID */
    protected function authUserId(): int
    {
        return $this->request->authUserId();
    }

    /** 仅消息需按结果动态变化时使用;常规接口直接返回 VO/数组,消息在路由 meta 的 message 声明 */
    protected function success(mixed $data = [], string $message = 'ok'): Response
    {
        return $this->responseFactory->success($this->request, $data, $message);
    }

    protected function fail(
        string $message,
        int $code = 40000,
        mixed $data = [],
        int $httpStatus = 400
    ): Response {
        return $this->responseFactory->fail($this->request, $message, $code, $data, $httpStatus);
    }
}
