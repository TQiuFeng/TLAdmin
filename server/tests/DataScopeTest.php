<?php

/**
 * 数据权限:全部 / 本部门 / 本部门及以下 / 自定义 / 仅本人,多个角色取并集,停用角色不算。
 *
 * 部门结构(测试时临时建,结束删除):
 *   根部门
 *   ├── 技术部(用户所在部门)
 *   │   └── 前端组
 *   └── 财务部
 * Author: qiufeng
 */

use app\common\service\system\DataScopeService;
use think\facade\Db;

/** 建测试部门树,返回各部门 ID */
$makeDepts = static function (TestContext $t): array {
    $t->needsDb();
    $now = time();
    $insert = static fn (int $parentId, string $name): int => (int) Db::table('tl_admin_dept')->insertGetId([
        'parent_id' => $parentId, 'name' => $name, 'leader' => '', 'phone' => '', 'email' => '',
        'sort' => 99, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
    ]);
    $root = $insert(0, '测试根部门');
    $tech = $insert($root, '测试技术部');
    $front = $insert($tech, '测试前端组');
    $finance = $insert($root, '测试财务部');
    $t->defer(static fn () => Db::table('tl_admin_dept')->where('id', 'in', [$root, $tech, $front, $finance])->delete());

    return compact('root', 'tech', 'front', 'finance');
};

$sorted = static function (array $ids): array {
    sort($ids);

    return $ids;
};

return [
    '全部数据' => static function (TestContext $t) use ($makeDepts): void {
        $d = $makeDepts($t);
        $admin = $t->createAdmin(['dept_id' => $d['tech']]);
        $t->createRole($admin['id'], 'all');
        $t->same(true, $t->make(DataScopeService::class)->resolve($admin['id'])['all']);
    },

    '本部门:只有自己部门,不含下级' => static function (TestContext $t) use ($makeDepts): void {
        $d = $makeDepts($t);
        $admin = $t->createAdmin(['dept_id' => $d['tech']]);
        $t->createRole($admin['id'], 'dept');
        $scope = $t->make(DataScopeService::class)->resolve($admin['id']);
        $t->same(false, $scope['all']);
        $t->same([$d['tech']], $scope['dept_ids']);
    },

    '本部门及以下:包含子孙部门,不含兄弟部门' => static function (TestContext $t) use ($makeDepts, $sorted): void {
        $d = $makeDepts($t);
        $admin = $t->createAdmin(['dept_id' => $d['tech']]);
        $t->createRole($admin['id'], 'dept_tree');
        $scope = $t->make(DataScopeService::class)->resolve($admin['id']);
        $t->same($sorted([$d['tech'], $d['front']]), $sorted($scope['dept_ids']));
    },

    '自定义部门' => static function (TestContext $t) use ($makeDepts): void {
        $d = $makeDepts($t);
        $admin = $t->createAdmin(['dept_id' => $d['tech']]);
        $t->createRole($admin['id'], 'custom', [], [$d['finance']]);
        $t->same([$d['finance']], $t->make(DataScopeService::class)->resolve($admin['id'])['dept_ids']);
    },

    '仅本人:没有部门范围,只能看自己的数据' => static function (TestContext $t) use ($makeDepts): void {
        $d = $makeDepts($t);
        $admin = $t->createAdmin(['dept_id' => $d['tech']]);
        $t->createRole($admin['id'], 'self');
        $scope = $t->make(DataScopeService::class)->resolve($admin['id']);
        $t->same(['all' => false, 'dept_ids' => [], 'user_id' => $admin['id']], $scope);
    },

    '多个角色取并集,停用的角色不算' => static function (TestContext $t) use ($makeDepts, $sorted): void {
        $d = $makeDepts($t);
        $admin = $t->createAdmin(['dept_id' => $d['tech']]);
        $t->createRole($admin['id'], 'dept');
        $t->createRole($admin['id'], 'custom', [], [$d['finance']]);
        $t->createRole($admin['id'], 'all', [], [], 0);

        $scope = $t->make(DataScopeService::class)->resolve($admin['id']);
        $t->same(false, $scope['all'], '停用的"全部数据"角色不生效');
        $t->same($sorted([$d['tech'], $d['finance']]), $sorted($scope['dept_ids']));
    },

    '超管不受数据权限限制' => static function (TestContext $t): void {
        $admin = $t->createAdmin(['is_super' => 1]);
        $t->same(true, $t->make(DataScopeService::class)->resolve($admin['id'])['all']);
    },
];
