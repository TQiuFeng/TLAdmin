<?php

namespace app\common\container;

/**
 * 轻量 IoC 容器,类比 Spring 的 ApplicationContext。
 *
 * - get():按类名取单例,未注册时反射构造器自动装配(类比 @Autowired 构造器注入);
 * - bind():注册工厂闭包,用于构造参数含标量(路径、数组)的类(类比 @Configuration 里的 @Bean 方法);
 * - instance():注册现成实例。
 * 构造器中的类类型参数递归解析;标量参数有默认值用默认值,否则必须 bind,缺失时报错指明类与参数名。
 * Author: qiufeng
 */
final class Container
{
    /** @var array<string, \Closure(self): object> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $instances = [];

    public function bind(string $class, \Closure $factory): void
    {
        $this->bindings[$class] = $factory;
    }

    public function instance(string $class, object $object): void
    {
        $this->instances[$class] = $object;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    public function get(string $class): object
    {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        $object = isset($this->bindings[$class])
            ? ($this->bindings[$class])($this)
            : $this->build($class);

        return $this->instances[$class] = $object;
    }

    private function build(string $class): object
    {
        if (!class_exists($class)) {
            throw new \RuntimeException("容器无法解析类:{$class}");
        }

        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return new $class();
        }

        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $arguments[] = $this->get($type->getName());
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }

            throw new \RuntimeException(
                "自动装配失败:{$class} 构造参数 \${$parameter->getName()} 是标量且无默认值,请在容器中 bind() 该类"
            );
        }

        return $reflection->newInstanceArgs($arguments);
    }
}
