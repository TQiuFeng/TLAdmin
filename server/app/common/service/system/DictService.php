<?php

namespace app\common\service\system;

use app\common\exception\BizException;
use think\facade\Db;

/**
 * 字典管理服务:字典类型 + 字典数据,供前端下拉框和代码生成器使用。
 * Author: qiufeng
 */
final class DictService
{
    public function typePaginate(array $filters, int $page, int $pageSize): array
    {
        $query = Db::table('tl_dict_type')->whereNull('delete_time');

        if (($filters['name'] ?? '') !== '') {
            $query->whereLike('name', '%' . $filters['name'] . '%');
        }
        if (($filters['code'] ?? '') !== '') {
            $query->whereLike('code', '%' . $filters['code'] . '%');
        }

        $total = (clone $query)->count();
        $list = $query->order('id')->page($page, $pageSize)->select()->toArray();

        return [
            'list' => $list,
            'pagination' => ['page' => $page, 'page_size' => $pageSize, 'total' => $total],
        ];
    }

    public function createType(array $data): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        $code = trim((string) ($data['code'] ?? ''));

        if ($name === '' || $code === '') {
            throw BizException::paramError('字典名称和编码不能为空');
        }
        if (Db::table('tl_dict_type')->where('code', $code)->whereNull('delete_time')->find()) {
            throw BizException::conflict('字典编码已存在');
        }

        $now = time();

        return (int) Db::table('tl_dict_type')->insertGetId([
            'name' => $name,
            'code' => $code,
            'status' => (int) ($data['status'] ?? 1),
            'remark' => (string) ($data['remark'] ?? ''),
            'create_time' => $now,
            'update_time' => $now,
        ]);
    }

    public function updateType(int $id, array $data): void
    {
        $type = Db::table('tl_dict_type')->where('id', $id)->whereNull('delete_time')->find();
        if (!$type) {
            throw BizException::notFound('字典类型不存在');
        }

        $updates = [];
        foreach (['name', 'remark'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (string) $data[$field];
            }
        }
        if (array_key_exists('status', $data)) {
            $updates['status'] = (int) $data['status'];
        }

        if ($updates !== []) {
            $updates['update_time'] = time();
            Db::table('tl_dict_type')->where('id', $id)->update($updates);
        }
    }

    public function deleteType(int $id): void
    {
        $type = Db::table('tl_dict_type')->where('id', $id)->whereNull('delete_time')->find();
        if (!$type) {
            throw BizException::notFound('字典类型不存在');
        }

        Db::startTrans();
        try {
            $now = time();
            Db::table('tl_dict_type')->where('id', $id)->update(['delete_time' => $now, 'update_time' => $now]);
            Db::table('tl_dict_data')->where('type_code', $type['code'])->whereNull('delete_time')
                ->update(['delete_time' => $now, 'update_time' => $now]);
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }
    }

    /** 按编码取字典项,前端下拉框接口 */
    public function dataByCode(string $code): array
    {
        return Db::table('tl_dict_data')
            ->where('type_code', $code)
            ->where('status', 1)
            ->whereNull('delete_time')
            ->field('id, label, value, sort, remark')
            ->order('sort')
            ->order('id')
            ->select()
            ->toArray();
    }

    public function dataPaginate(string $typeCode, int $page, int $pageSize): array
    {
        $query = Db::table('tl_dict_data')->where('type_code', $typeCode)->whereNull('delete_time');

        $total = (clone $query)->count();
        $list = $query->order('sort')->order('id')->page($page, $pageSize)->select()->toArray();

        return [
            'list' => $list,
            'pagination' => ['page' => $page, 'page_size' => $pageSize, 'total' => $total],
        ];
    }

    public function createData(array $data): int
    {
        $typeCode = trim((string) ($data['type_code'] ?? ''));
        $label = trim((string) ($data['label'] ?? ''));
        $value = trim((string) ($data['value'] ?? ''));

        if ($typeCode === '' || $label === '' || $value === '') {
            throw BizException::paramError('字典类型、标签和值不能为空');
        }
        if (!Db::table('tl_dict_type')->where('code', $typeCode)->whereNull('delete_time')->find()) {
            throw BizException::paramError('字典类型不存在');
        }

        $now = time();

        return (int) Db::table('tl_dict_data')->insertGetId([
            'type_code' => $typeCode,
            'label' => $label,
            'value' => $value,
            'sort' => (int) ($data['sort'] ?? 0),
            'status' => (int) ($data['status'] ?? 1),
            'remark' => (string) ($data['remark'] ?? ''),
            'create_time' => $now,
            'update_time' => $now,
        ]);
    }

    public function updateData(int $id, array $data): void
    {
        if (!Db::table('tl_dict_data')->where('id', $id)->whereNull('delete_time')->find()) {
            throw BizException::notFound('字典项不存在');
        }

        $updates = [];
        foreach (['label', 'value', 'remark'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (string) $data[$field];
            }
        }
        foreach (['sort', 'status'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (int) $data[$field];
            }
        }

        if ($updates !== []) {
            $updates['update_time'] = time();
            Db::table('tl_dict_data')->where('id', $id)->update($updates);
        }
    }

    public function deleteData(int $id): void
    {
        if (!Db::table('tl_dict_data')->where('id', $id)->whereNull('delete_time')->find()) {
            throw BizException::notFound('字典项不存在');
        }

        Db::table('tl_dict_data')->where('id', $id)->update([
            'delete_time' => time(),
            'update_time' => time(),
        ]);
    }
}
