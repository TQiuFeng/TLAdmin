<?php

namespace app\common\service\system;

use app\common\exception\BizException;
use app\common\http\Request;
use app\common\service\integration\StorageService;
use app\common\support\Tools;
use think\facade\Db;

/**
 * 附件服务。
 *
 * 直传流程:policy 签发凭证 → 前端直传云端 → register 凭票据登记入库。
 * 本地磁盘走 uploadLocal 后端兜底(接收即登记)。
 * Author: qiufeng
 */
final class AttachmentService
{
    public function __construct(
        private readonly StorageService $storageService,
        private readonly string $rootPath
    ) {
    }

    /** 直传凭证 */
    public function policy(string $filename, int $size, string $mime, int $userId): array
    {
        return $this->storageService->buildUploadPolicy($filename, $size, $mime, $userId);
    }

    /** 云端直传完成后登记 */
    public function register(string $key, int $userId): array
    {
        if ($key === '') {
            throw BizException::paramError('key 不能为空');
        }

        $ticket = $this->storageService->consumeTicket($key);

        if ((int) ($ticket['user_id'] ?? 0) !== $userId) {
            throw BizException::forbidden('上传凭证与当前账号不匹配');
        }
        if (($ticket['disk'] ?? '') === 'local') {
            throw BizException::paramError('本地磁盘请使用 /adminapi/attachments/upload 接口上传');
        }

        return $this->insert($ticket, '', $userId);
    }

    /** 本地磁盘兜底上传:接收 multipart 文件并登记 */
    public function uploadLocal(Request $request): array
    {
        $key = (string) $request->input('key', '');
        if ($key === '') {
            throw BizException::paramError('key 不能为空,请先获取直传凭证');
        }

        $ticket = $this->storageService->consumeTicket($key);
        if ((int) ($ticket['user_id'] ?? 0) !== $request->authUserId()) {
            throw BizException::forbidden('上传凭证与当前账号不匹配');
        }
        if (($ticket['disk'] ?? '') !== 'local') {
            throw BizException::paramError('当前默认磁盘不是本地存储,请直传云端后调用登记接口');
        }

        $file = $request->file('file');
        if ($file === null) {
            throw BizException::paramError('缺少上传文件字段 file');
        }

        // 以实际接收到的文件重新校验,不信任 policy 阶段的声明值
        $this->storageService->assertUploadAllowed((string) $file['name'], (int) $file['size']);

        $safeKey = Tools::assertSafePath($key);
        $root = $this->localRoot();
        $target = $root . '/' . $safeKey;

        if (!is_dir(dirname($target)) && !mkdir(dirname($target), 0755, true) && !is_dir(dirname($target))) {
            throw new \RuntimeException('创建上传目录失败');
        }

        $moved = is_uploaded_file((string) $file['tmp_name'])
            ? move_uploaded_file((string) $file['tmp_name'], $target)
            : rename((string) $file['tmp_name'], $target);
        if (!$moved) {
            throw new \RuntimeException('保存上传文件失败');
        }

        $ticket['size'] = (int) $file['size'];
        $ticket['mime'] = mime_content_type($target) ?: (string) ($file['type'] ?? '');

        return $this->insert($ticket, sha1_file($target) ?: '', $request->authUserId());
    }

    public function paginate(array $filters, int $page, int $pageSize): array
    {
        $query = Db::table('tl_attachment')->whereNull('delete_time');

        if (($filters['name'] ?? '') !== '') {
            $query->whereLike('name', '%' . $filters['name'] . '%');
        }
        if (($filters['disk'] ?? '') !== '') {
            $query->where('disk', (string) $filters['disk']);
        }
        if (($filters['ext'] ?? '') !== '') {
            $query->where('ext', strtolower((string) $filters['ext']));
        }

        $total = (clone $query)->count();
        $list = $query->order('id', 'desc')->page($page, $pageSize)->select()->toArray();

        return [
            'list' => $list,
            'pagination' => ['page' => $page, 'page_size' => $pageSize, 'total' => $total],
        ];
    }

    /** 删除附件:尽力删除物理文件/远端对象,记录软删 */
    public function delete(int $id): array
    {
        $attachment = $this->mustFind($id);

        $remoteDeleted = $this->storageService->deleteObject(
            (string) $attachment['disk'],
            (string) $attachment['path'],
            $this->localRoot()
        );

        Db::table('tl_attachment')->where('id', $id)->update(['delete_time' => time()]);

        return ['remote_deleted' => $remoteDeleted];
    }

    /** 访问 URL:私有桶返回临时签名 URL */
    public function url(int $id, int $expires = 600): array
    {
        $attachment = $this->mustFind($id);

        return [
            'url' => $this->storageService->temporaryUrl(
                (string) $attachment['disk'],
                (string) $attachment['path'],
                max(60, min(86400, $expires))
            ),
            'expires_in' => max(60, min(86400, $expires)),
        ];
    }

    private function insert(array $ticket, string $sha1, int $userId): array
    {
        $key = (string) $ticket['key'];
        $disk = (string) $ticket['disk'];

        $id = Db::table('tl_attachment')->insertGetId([
            'name' => (string) ($ticket['name'] ?? ''),
            'path' => $key,
            'disk' => $disk,
            'url' => $this->storageService->publicUrl($disk, $key),
            'mime' => (string) ($ticket['mime'] ?? ''),
            'ext' => Tools::extension($key),
            'size' => (int) ($ticket['size'] ?? 0),
            'sha1' => $sha1,
            'uploader_id' => $userId,
            'create_time' => time(),
        ]);

        return Db::table('tl_attachment')->where('id', $id)->find() ?: [];
    }

    private function localRoot(): string
    {
        $root = (string) ($this->storageService->config()['disks']['local']['root'] ?? 'public/storage');

        return rtrim($this->rootPath, '/') . '/' . trim($root, '/');
    }

    private function mustFind(int $id): array
    {
        $attachment = Db::table('tl_attachment')->where('id', $id)->whereNull('delete_time')->find();
        if (!$attachment) {
            throw BizException::notFound('附件不存在');
        }

        return $attachment;
    }
}
