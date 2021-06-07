<?php

declare(strict_types=1);

namespace Golly\Hydrate;

use Doctrine\Common\Annotations\AnnotationReader;
use Golly\Hydrate\Annotations\Source;
use Golly\Hydrate\Contracts\EntityInterface;
use Golly\Hydrate\Contracts\ReflectionInterface;
use ReflectionClass;
use ReflectionProperty;


/**
 * 字段映射，必须保证类名的唯一性
 *
 * Class Reflection
 * @package Golly\Hydrate
 */
class Reflection implements ReflectionInterface
{

    /**
     * @var array
     */
    protected static array $reflectProperties = [];

    /**
     * 将数组赋值到对象
     *
     * @param array $data
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public static function hydrate(array $data, EntityInterface $entity): EntityInterface
    {
        $reflectProperties = self::getReflectProperties($entity);
        $annotationReader = new AnnotationReader();
        foreach ($reflectProperties as $name => $property) {
            $defaultValue = $property->getValue($entity);
            $column = $annotationReader->getPropertyAnnotation($property, Source::class);
            if ($column instanceof Source) { // 映射关系
                $value = ArrayHelper::get($data, $column->field, $defaultValue);
            } else {
                $value = ArrayHelper::get($data, $name, $defaultValue);
            }
            $property->setValue($entity, $value);
        }

        return $entity;
    }


    /**
     * 对象转为数组
     *
     * @param EntityInterface $entity
     * @param string $format
     * @return array
     */
    public static function extract(EntityInterface $entity, string $format = 'snake'): array
    {
        $result = [];
        $properties = self::getReflectProperties($entity);
        foreach ($properties as $name => $property) {
            $value = $property->getValue($entity);
            if (is_array($value)) {
                $arrValue = [];
                foreach ($value as $key => $item) {
                    if ($item instanceof EntityInterface) {
                        $arrValue[$key] = static::extract($item, $format);
                    } else {
                        $arrValue[$key] = $item;
                    }
                }
                $value = $arrValue;
            } elseif ($value instanceof EntityInterface) {
                $value = static::extract($value, $format);
            }
            // 格式转化
            $name = match ($format) {
                'camel' => StringHelper::camel($name),
                'studly' => StringHelper::studly($name),
                default => StringHelper::snake($name),
            };
            $result[$name] = $value;
        }

        return $result;
    }

    /**
     * 获取要转化对象的属性
     *
     * @param EntityInterface $entity
     * @return ReflectionProperty[]
     */
    protected static function getReflectProperties(EntityInterface $entity): array
    {
        $key = get_class($entity);
        if (isset(static::$reflectProperties[$key])) {
            return static::$reflectProperties[$key];
        }
        $properties = [];
        $reflectProperties = (new ReflectionClass($entity))->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($reflectProperties as $property) {
            $property->setAccessible(true);
            $properties[$property->getName()] = $property;
        }
        static::$reflectProperties[$key] = $properties;

        return $properties;
    }
}
