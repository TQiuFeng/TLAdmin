<?php

namespace app\adminapi\vo;

use app\common\openapi\ApiField;

/**
 * VO 基类:数组(DB 行/Service 结果)与 VO 对象的双向转换。
 *
 * 用法:
 *   $vo = TokenVo::from($tokens);          // 数组 → VO,按属性类型自动转换
 *   $list = MenuVo::list($rows);           // 行列表 → VO 列表
 *   $vo->toArray();                        // VO → 数组(递归),响应序列化使用
 *
 * 映射规则:
 *   - 数据中不存在的键跳过(属性保持未初始化,toArray 时不输出,适合条件字段)
 *   - 属性类型为其他 VO 类时递归 from
 *   - array 属性按 ApiField 的 listOf 映射元素(VO 类或标量)
 *   - object 属性把数组转为 stdClass,保证 JSON 输出 {} 而非 []
 * Author: qiufeng
 */
abstract class BaseVo implements \JsonSerializable
{
    public static function from(array $data): static
    {
        $vo = new static();

        foreach ((new \ReflectionClass(static::class))->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic() || !array_key_exists($property->getName(), $data)) {
                continue;
            }

            $value = $data[$property->getName()];
            $type = $property->getType();

            if ($type instanceof \ReflectionNamedType) {
                if ($value === null) {
                    if (!$type->allowsNull()) {
                        continue;
                    }
                } elseif ($type->isBuiltin()) {
                    $value = self::castBuiltin($property, $type->getName(), $value);
                } elseif (is_subclass_of($type->getName(), self::class) && is_array($value)) {
                    $value = $type->getName()::from($value);
                }
            }

            $vo->{$property->getName()} = $value;
        }

        return $vo;
    }

    /** @return static[] */
    public static function list(iterable $rows): array
    {
        $items = [];
        foreach ($rows as $row) {
            $items[] = static::from((array) $row);
        }

        return $items;
    }

    public function toArray(): array
    {
        $result = [];

        foreach ((new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic() || !$property->isInitialized($this)) {
                continue;
            }

            $result[$property->getName()] = self::export($property->getValue($this));
        }

        return $result;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    private static function export(mixed $value): mixed
    {
        if ($value instanceof self) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return array_map(self::export(...), $value);
        }

        return $value;
    }

    private static function castBuiltin(\ReflectionProperty $property, string $type, mixed $value): mixed
    {
        return match ($type) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => (bool) $value,
            'string' => (string) $value,
            'object' => is_array($value) ? (object) $value : $value,
            'array' => self::castList($property, (array) $value),
            default => $value,
        };
    }

    private static function castList(\ReflectionProperty $property, array $value): array
    {
        $attributes = $property->getAttributes(ApiField::class);
        $listOf = $attributes !== [] ? $attributes[0]->newInstance()->listOf : null;

        if ($listOf === null) {
            return $value;
        }

        if (is_subclass_of($listOf, self::class)) {
            return array_map(static fn (mixed $row): self => $listOf::from((array) $row), $value);
        }

        return array_map(static fn (mixed $item): mixed => match ($listOf) {
            'int' => (int) $item,
            'float' => (float) $item,
            'bool' => (bool) $item,
            'string' => (string) $item,
            default => $item,
        }, $value);
    }
}
