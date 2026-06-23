<?php

namespace app\common\service\system;

use app\common\exception\BizException;
use app\common\support\Tools;
use think\facade\Db;

/**
 * 菜单管理服务:目录/菜单/按钮/接口权限节点的树形维护。
 * Author: qiufeng
 */
final class MenuService
{
    private const TYPES = ['catalog', 'menu', 'button', 'api'];

    public function tree(): array
    {
        $menus = Db::table('tl_admin_menu')->order('sort')->order('id')->select()->toArray();

        return Tools::tree($menus);
    }

    public function create(array $data): int
    {
        $this->validate($data);

        $now = time();

        return (int) Db::table('tl_admin_menu')->insertGetId([
            'parent_id' => (int) ($data['parent_id'] ?? 0),
            'type' => (string) $data['type'],
            'title' => trim((string) $data['title']),
            'name' => (string) ($data['name'] ?? ''),
            'path' => (string) ($data['path'] ?? ''),
            'component' => (string) ($data['component'] ?? ''),
            'icon' => (string) ($data['icon'] ?? ''),
            'permission' => (string) ($data['permission'] ?? ''),
            'sort' => (int) ($data['sort'] ?? 0),
            'visible' => (int) ($data['visible'] ?? 1),
            'status' => (int) ($data['status'] ?? 1),
            'create_time' => $now,
            'update_time' => $now,
        ]);
    }

    public function update(int $id, array $data): void
    {
        $this->mustFind($id);

        if (isset($data['parent_id']) && (int) $data['parent_id'] === $id) {
            throw BizException::paramError('父级不能选择自己');
        }
        if (isset($data['type']) && !in_array($data['type'], self::TYPES, true)) {
            throw BizException::paramError('菜单类型不合法');
        }

        $updates = [];
        foreach (['type', 'title', 'name', 'path', 'component', 'icon', 'permission'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (string) $data[$field];
            }
        }
        foreach (['parent_id', 'sort', 'visible', 'status'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = (int) $data[$field];
            }
        }

        if ($updates !== []) {
            $updates['update_time'] = time();
            Db::table('tl_admin_menu')->where('id', $id)->update($updates);
        }
    }

    public function delete(int $id): void
    {
        $this->mustFind($id);

        if (Db::table('tl_admin_menu')->where('parent_id', $id)->count() > 0) {
            throw BizException::conflict('存在子节点,请先删除子节点');
        }

        Db::startTrans();
        try {
            Db::table('tl_admin_menu')->where('id', $id)->delete();
            Db::table('tl_admin_role_menu')->where('menu_id', $id)->delete();
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }
    }

    private function validate(array $data): void
    {
        if (trim((string) ($data['title'] ?? '')) === '') {
            throw BizException::paramError('菜单名称不能为空');
        }
        if (!in_array($data['type'] ?? '', self::TYPES, true)) {
            throw BizException::paramError('菜单类型不合法,可选:catalog/menu/button/api');
        }

        $parentId = (int) ($data['parent_id'] ?? 0);
        if ($parentId > 0 && !Db::table('tl_admin_menu')->where('id', $parentId)->find()) {
            throw BizException::paramError('父级节点不存在');
        }
    }

    private function mustFind(int $id): array
    {
        $menu = Db::table('tl_admin_menu')->where('id', $id)->find();
        if (!$menu) {
            throw BizException::notFound('菜单不存在');
        }

        return $menu;
    }
}
