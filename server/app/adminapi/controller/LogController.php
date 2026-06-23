<?php

namespace app\adminapi\controller;

use app\adminapi\vo\LoginLogVo;
use app\adminapi\vo\OperationLogVo;
use app\adminapi\vo\PageVo;
use app\adminapi\vo\ThirdPartyLogVo;
use app\common\config\ConfigRepository;
use app\common\response\ApiResponseFactory;
use app\common\router\GetMapping;
use app\common\router\RestController;
use app\common\service\log\LoginLogService;
use app\common\service\log\OperationLogService;
use app\common\service\log\ThirdPartyLogService;

/**
 * 日志查询控制器:操作日志、登录日志、第三方请求日志。
 * Author: qiufeng
 */
#[RestController('/adminapi/system/logs', tag: '日志')]
final class LogController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly OperationLogService $operationLogService,
        private readonly LoginLogService $loginLogService,
        private readonly ThirdPartyLogService $thirdPartyLogService
    ) {
        parent::__construct($responseFactory, $config);
    }

    #[GetMapping('/operations', permission: 'system:operation-log:list', summary: '操作日志', listOf: OperationLogVo::class)]
    public function operations(): PageVo
    {
        return PageVo::of($this->operationLogService->paginate(
            [
                'username' => (string) $this->query('username', ''),
                'path' => (string) $this->query('path', ''),
                'request_id' => (string) $this->query('request_id', ''),
                'start_time' => (string) $this->query('start_time', ''),
                'end_time' => (string) $this->query('end_time', ''),
            ],
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20)))
        ), OperationLogVo::class);
    }

    #[GetMapping('/logins', permission: 'system:login-log:list', summary: '登录日志', listOf: LoginLogVo::class)]
    public function logins(): PageVo
    {
        return PageVo::of($this->loginLogService->paginate(
            [
                'username' => (string) $this->query('username', ''),
                'status' => (string) $this->query('status', ''),
                'ip' => (string) $this->query('ip', ''),
                'start_time' => (string) $this->query('start_time', ''),
                'end_time' => (string) $this->query('end_time', ''),
            ],
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20)))
        ), LoginLogVo::class);
    }

    #[GetMapping('/third-party', permission: 'system:third-party-log:list', summary: '第三方请求日志', listOf: ThirdPartyLogVo::class)]
    public function thirdParty(): PageVo
    {
        return PageVo::of($this->thirdPartyLogService->paginate(
            [
                'host' => (string) $this->query('host', ''),
                'ok' => (string) $this->query('ok', ''),
                'start_time' => (string) $this->query('start_time', ''),
                'end_time' => (string) $this->query('end_time', ''),
            ],
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20)))
        ), ThirdPartyLogVo::class);
    }
}
