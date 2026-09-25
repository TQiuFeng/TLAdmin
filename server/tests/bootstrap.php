<?php

/**
 * 测试引导:加载环境、数据库,并按 public/index.php 的方式准备容器;提供断言上下文。
 * Author: qiufeng
 */

declare(strict_types=1);

use app\common\config\ConfigRepository;
use app\common\config\EnvLoader;
use app\common\container\Container;
use app\common\database\DbBootstrap;
use app\common\service\geo\IpLocationService;
use think\facade\Db;

$rootPath = dirname(__DIR__);
require $rootPath . '/vendor/autoload.php';

EnvLoader::load($rootPath . '/.env');
DbBootstrap::init();

/** 用例被跳过(如没有数据库) */
final class TestSkipped extends RuntimeException
{
}

/** 断言失败 */
final class AssertionFailed extends RuntimeException
{
}

/** 每个用例一个上下文:断言、依赖、用例结束后的清理 */
final class TestContext
{
    private static ?bool $dbAvailable = null;

    private static ?Container $container = null;

    /** @var callable[] */
    private array $cleanups = [];

    public function needsDb(): void
    {
        if (self::$dbAvailable === null) {
            try {
                Db::query('SELECT 1');
                self::$dbAvailable = true;
            } catch (Throwable) {
                self::$dbAvailable = false;
            }
        }
        if (!self::$dbAvailable) {
            throw new TestSkipped('数据库不可用');
        }
    }

    private static ?bool $redisAvailable = null;

    public function needsRedis(): void
    {
        if (self::$redisAvailable === null) {
            try {
                \app\common\cache\RedisClient::connection()->ping();
                self::$redisAvailable = true;
            } catch (Throwable) {
                self::$redisAvailable = false;
            }
        }
        if (!self::$redisAvailable) {
            throw new TestSkipped('Redis 不可用');
        }
    }

    /**
     * 建一个测试管理员(用例结束物理删除,连同角色关联)。
     *
     * @param array $overrides 覆盖字段,如 status、dept_id、is_super
     * @return array{id: int, username: string, password: string}
     */
    public function createAdmin(array $overrides = []): array
    {
        $this->needsDb();
        $now = time();
        $username = 't_admin_' . bin2hex(random_bytes(4));
        $password = 'Pwd-' . bin2hex(random_bytes(6));
        $id = (int) Db::table('tl_admin_user')->insertGetId($overrides + [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'nickname' => '测试管理员',
            'is_super' => 0,
            'status' => 1,
            'dept_id' => 0,
            'create_time' => $now,
            'update_time' => $now,
        ]);
        $this->defer(static function () use ($id): void {
            Db::table('tl_admin_user_role')->where('user_id', $id)->delete();
            Db::table('tl_admin_user')->where('id', $id)->delete();
        });

        return ['id' => $id, 'username' => $username, 'password' => $password];
    }

    /**
     * 建一个测试角色并分给用户(用例结束删除角色及其菜单、部门关联)。
     *
     * @param string[] $permissions 菜单/按钮/接口权限标识
     * @param int[] $customDeptIds data_scope=custom 时的部门
     */
    public function createRole(int $userId, string $dataScope = 'all', array $permissions = [], array $customDeptIds = [], int $status = 1): int
    {
        $now = time();
        $suffix = bin2hex(random_bytes(4));
        $roleId = (int) Db::table('tl_admin_role')->insertGetId([
            'name' => '测试角色' . $suffix, 'code' => 't_role_' . $suffix, 'data_scope' => $dataScope,
            'sort' => 99, 'status' => $status, 'remark' => '', 'create_time' => $now, 'update_time' => $now,
        ]);
        foreach (Db::table('tl_admin_menu')->where('permission', 'in', $permissions ?: ['__none__'])->column('id') as $menuId) {
            Db::table('tl_admin_role_menu')->insert(['role_id' => $roleId, 'menu_id' => $menuId]);
        }
        foreach ($customDeptIds as $deptId) {
            Db::table('tl_admin_role_dept')->insert(['role_id' => $roleId, 'dept_id' => $deptId]);
        }
        Db::table('tl_admin_user_role')->insert(['user_id' => $userId, 'role_id' => $roleId]);
        $this->defer(static function () use ($roleId): void {
            Db::table('tl_admin_role_menu')->where('role_id', $roleId)->delete();
            Db::table('tl_admin_role_dept')->where('role_id', $roleId)->delete();
            Db::table('tl_admin_user_role')->where('role_id', $roleId)->delete();
            Db::table('tl_admin_role')->where('id', $roleId)->delete();
        });

        return $roleId;
    }

    /** 与 public/index.php 相同的容器绑定,其余自动装配 */
    public function make(string $class): object
    {
        if (self::$container === null) {
            $root = dirname(__DIR__);
            $container = new Container();
            $container->instance(Container::class, $container);
            $container->bind(ConfigRepository::class, static fn (): ConfigRepository => new ConfigRepository(
                [
                    $root . '/config/app.php',
                    $root . '/config/api.php',
                    $root . '/config/integration.php',
                    $root . '/config/security.php',
                ],
                $root . '/runtime/config/system.json'
            ));
            $container->bind(IpLocationService::class, static fn (): IpLocationService => new IpLocationService($root . '/resources/geo/ip_ranges.csv'));
            self::$container = $container;
        }

        return self::$container->get($class);
    }

    /** 用例结束后执行(无论成败),用于删除测试数据、临时文件 */
    public function defer(callable $cleanup): void
    {
        $this->cleanups[] = $cleanup;
    }

    public function cleanup(): void
    {
        foreach (array_reverse($this->cleanups) as $cleanup) {
            try {
                $cleanup();
            } catch (Throwable) {
                // 清理失败不影响结果
            }
        }
    }

    public function same(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            $this->fail(($message !== '' ? "{$message}:" : '') . '期望 ' . $this->dump($expected) . ',实际 ' . $this->dump($actual));
        }
    }

    public function true(bool $condition, string $message): void
    {
        if (!$condition) {
            $this->fail($message);
        }
    }

    public function contains(string $needle, string $haystack, string $message = ''): void
    {
        if (!str_contains($haystack, $needle)) {
            $this->fail(($message !== '' ? "{$message}:" : '') . "未包含 {$needle}");
        }
    }

    public function notContains(string $needle, string $haystack, string $message = ''): void
    {
        if (str_contains($haystack, $needle)) {
            $this->fail(($message !== '' ? "{$message}:" : '') . "不应包含 {$needle}");
        }
    }

    /**
     * 断言抛出指定异常,返回异常对象便于进一步检查。
     *
     * @template T of Throwable
     * @param class-string<T> $class
     * @return T
     */
    public function throws(string $class, callable $callback): Throwable
    {
        try {
            $callback();
        } catch (Throwable $e) {
            if ($e instanceof $class) {
                return $e;
            }
            $this->fail("期望抛出 {$class},实际抛出 " . $e::class . ":{$e->getMessage()}");
        }
        $this->fail("期望抛出 {$class},实际没有抛出");
    }

    public function fail(string $message): never
    {
        throw new AssertionFailed($message);
    }

    private function dump(mixed $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: var_export($value, true);
    }
}
