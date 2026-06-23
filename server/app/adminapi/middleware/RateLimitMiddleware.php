<?php

namespace app\adminapi\middleware;

use app\common\cache\RedisClient;
use app\common\config\ConfigRepository;
use app\common\http\Request;
use app\common\http\Response;
use app\common\response\ApiResponseFactory;

/**
 * 限流中间件。
 *
 * 固定窗口(1 秒)计数:已登录按用户 ID、未登录按 IP,
 * 阈值取系统配置 security.rate_limit.per_second(0 表示不限流),超限返回 429。
 * Author: qiufeng
 */
final class RateLimitMiddleware
{
    public function __construct(
        private readonly ApiResponseFactory $responseFactory,
        private readonly ConfigRepository $config
    ) {
    }

    public function __invoke(Request $request, array $meta): ?Response
    {
        $limit = (int) $this->config->get('security.rate_limit.per_second', 10);
        if ($limit <= 0) {
            return null;
        }

        $subject = $request->authUserId() > 0
            ? 'u:' . $request->authUserId()
            : 'ip:' . $request->clientIp();

        // key 带秒级时间戳,窗口自然滚动;TTL 2 秒兜底过期
        $count = RedisClient::increment('rate-limit:' . $subject . ':' . time(), 2);

        return $count > $limit
            ? $this->responseFactory->fail($request, '请求过于频繁,请稍后再试', 42900, [], 429)
            : null;
    }
}
