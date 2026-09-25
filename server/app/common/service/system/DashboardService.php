<?php

namespace app\common\service\system;

use app\common\database\Mongo;
use app\common\service\auth\AuthService;
use app\common\service\log\LoginLogService;
use app\common\service\log\OperationLogService;
use think\facade\Db;

/**
 * 仪表盘统计。
 *
 * 每一块数据按对应的查看权限单独判断:没有"管理员列表"权限就不返回管理员数,以此类推,
 * 仪表盘不会成为绕过权限看数据的口子。日志在 MongoDB,连不上时日志类统计直接省略。
 * Author: qiufeng
 */
final class DashboardService
{
    private const TREND_DAYS = 7;

    public function __construct(private readonly AuthService $authService)
    {
    }

    /** @return array{stats: array, operation_trend?: array, recent_logins?: array} */
    public function overview(int $userId): array
    {
        $can = fn (string $permission): bool => $this->authService->hasPermission($userId, $permission);
        $todayStart = strtotime('today');

        $stats = [];
        if ($can('system:user:list')) {
            $stats[] = [
                'key' => 'admins',
                'label' => '管理员',
                'value' => $this->count('tl_admin_user'),
                'hint' => '启用 ' . $this->count('tl_admin_user', ['status' => 1]),
            ];
        }
        if ($can('system:role:list')) {
            $stats[] = [
                'key' => 'roles',
                'label' => '角色',
                'value' => $this->count('tl_admin_role'),
                'hint' => '启用 ' . $this->count('tl_admin_role', ['status' => 1]),
            ];
        }
        if ($can('member:user:list')) {
            $newToday = (int) Db::table('tl_user')->whereNull('delete_time')->where('create_time', '>=', $todayStart)->count();
            $stats[] = [
                'key' => 'members',
                'label' => '会员',
                'value' => $this->count('tl_user'),
                'hint' => "今日新增 {$newToday}",
            ];
        }

        $result = ['stats' => $stats];

        if ($can('system:login-log:list')) {
            $logins = $this->safeMongo(function () use ($todayStart): array {
                $collection = Mongo::collection(LoginLogService::COLLECTION);
                $success = $collection->countDocuments(['create_time' => ['$gte' => $todayStart], 'status' => 1]);
                $failed = $collection->countDocuments(['create_time' => ['$gte' => $todayStart], 'status' => 0]);
                $recent = $collection->find([], [
                    'sort' => ['create_time' => -1],
                    'limit' => 5,
                    'projection' => ['_id' => 0, 'username' => 1, 'ip' => 1, 'location' => 1, 'status' => 1, 'create_time' => 1],
                ])->toArray();

                return ['success' => $success, 'failed' => $failed, 'recent' => $recent];
            });
            if ($logins !== null) {
                $result['stats'][] = [
                    'key' => 'logins',
                    'label' => '今日登录',
                    'value' => (int) $logins['success'],
                    'hint' => "失败 {$logins['failed']} 次",
                ];
                $result['recent_logins'] = array_map(static fn (array $row): array => [
                    'username' => (string) ($row['username'] ?? ''),
                    'ip' => (string) ($row['ip'] ?? ''),
                    'location' => (string) ($row['location'] ?? ''),
                    'status' => (int) ($row['status'] ?? 0),
                    'create_time' => (int) ($row['create_time'] ?? 0),
                ], $logins['recent']);
            }
        }

        if ($can('system:operation-log:list')) {
            $trend = $this->safeMongo(function () use ($todayStart): array {
                $collection = Mongo::collection(OperationLogService::COLLECTION);
                $points = [];
                for ($i = self::TREND_DAYS - 1; $i >= 0; $i--) {
                    $start = $todayStart - $i * 86400;
                    $points[] = [
                        'date' => date('m-d', $start),
                        'count' => $collection->countDocuments(['create_time' => ['$gte' => $start, '$lt' => $start + 86400]]),
                    ];
                }

                return $points;
            });
            if ($trend !== null) {
                $result['operation_trend'] = $trend;
            }
        }

        return $result;
    }

    /** 未删除记录数,可附加等值条件 */
    private function count(string $table, array $where = []): int
    {
        return (int) Db::table($table)->whereNull('delete_time')->where($where)->count();
    }

    /** MongoDB 不可用时返回 null,仪表盘其他部分照常显示 */
    private function safeMongo(callable $callback): ?array
    {
        try {
            return $callback();
        } catch (\Throwable) {
            return null;
        }
    }
}
