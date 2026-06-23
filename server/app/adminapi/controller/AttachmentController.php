<?php

namespace app\adminapi\controller;

use app\adminapi\vo\AttachmentPolicyVo;
use app\adminapi\vo\AttachmentUrlVo;
use app\adminapi\vo\AttachmentVo;
use app\adminapi\vo\PageVo;
use app\common\config\ConfigRepository;
use app\common\exception\BizException;
use app\common\http\Response;
use app\common\response\ApiResponseFactory;
use app\common\router\DeleteMapping;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\RestController;
use app\common\service\system\AttachmentService;

/**
 * 附件控制器:直传凭证、登记、本地兜底上传、列表、删除、临时 URL。
 * Author: qiufeng
 */
#[RestController('/adminapi/attachments', tag: '附件')]
final class AttachmentController extends BaseController
{
    public function __construct(
        ApiResponseFactory $responseFactory,
        ConfigRepository $config,
        private readonly AttachmentService $service
    ) {
        parent::__construct($responseFactory, $config);
    }

    /** 获取直传凭证 */
    #[PostMapping('/policy', summary: '获取直传凭证 {filename, size, mime}')]
    public function policy(): AttachmentPolicyVo
    {
        $filename = (string) $this->input('filename', '');

        if ($filename === '') {
            throw BizException::paramError('filename 不能为空');
        }

        return AttachmentPolicyVo::from($this->service->policy(
            $filename,
            (int) $this->input('size', 0),
            (string) $this->input('mime', ''),
            $this->authUserId()
        ));
    }

    /** 云端直传完成后登记 */
    #[PostMapping(summary: '云端直传完成后登记 {key}', message: '上传登记成功')]
    public function register(): AttachmentVo
    {
        return AttachmentVo::from(
            $this->service->register((string) $this->input('key', ''), $this->authUserId())
        );
    }

    /** 本地磁盘兜底上传(multipart,file 字段) */
    #[PostMapping('/upload', summary: '本地磁盘兜底上传(multipart:key + file)', message: '上传成功')]
    public function upload(): AttachmentVo
    {
        return AttachmentVo::from($this->service->uploadLocal($this->request));
    }

    #[GetMapping(permission: 'system:attachment:list', summary: '附件列表', listOf: AttachmentVo::class)]
    public function index(): PageVo
    {
        return PageVo::of($this->service->paginate(
            [
                'name' => (string) $this->query('name', ''),
                'disk' => (string) $this->query('disk', ''),
                'ext' => (string) $this->query('ext', ''),
            ],
            max(1, (int) $this->query('page', 1)),
            min(100, max(1, (int) $this->query('page_size', 20)))
        ), AttachmentVo::class);
    }

    /** 删除结果消息依赖远端删除是否成功,保留手动包装 */
    #[DeleteMapping('/{id}', permission: 'system:attachment:delete', summary: '删除附件(含远端对象)', response: \app\adminapi\vo\AttachmentDeleteVo::class)]
    public function destroy(): Response
    {
        $result = $this->service->delete((int) $this->param('id'));

        return $this->success(
            $result,
            $result['remote_deleted'] ? '删除成功' : '记录已删除,远端文件删除失败(请检查存储配置)'
        );
    }

    #[GetMapping('/{id}/url', summary: '附件访问 URL(私有桶返回临时签名)')]
    public function url(): AttachmentUrlVo
    {
        return AttachmentUrlVo::from($this->service->url(
            (int) $this->param('id'),
            (int) $this->query('expires', 600)
        ));
    }
}
