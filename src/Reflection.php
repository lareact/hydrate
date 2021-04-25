<?php

namespace Golly\Hydrate;

use Doctrine\Common\Annotations\AnnotationReader;
use Golly\Hydrate\Annotations\Mapping;
use Golly\Hydrate\Contracts\EntityInterface;
use ReflectionClass;
use ReflectionProperty;


/**
 * 字段映射，必须保证类名的唯一性
 *
 * Class Reflection
 * @package Golly\Hydrate
 */
class Reflection
{

    /**
     * @var array
     */
    protected static $reflectProperties = [];

    /**
     * 将数组赋值到对象
     *
     * @param array $data
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function hydrate(array $data, EntityInterface $entity)
    {
        $reflectProperties = $this->getReflectProperties($entity);
        $annotationReader = new AnnotationReader();
        foreach ($reflectProperties as $name => $property) {
            $defaultValue = $property->getValue($entity);
            $column = $annotationReader->getPropertyAnnotation($property, Mapping::class);
            if ($column instanceof Mapping) { // 映射关系
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
    public function extract(EntityInterface $entity, $format = 'snake')
    {
        $result = [];
        $properties = self::getReflectProperties($entity);
        foreach ($properties as $name => $property) {
            $value = $property->getValue($entity);
            if (is_array($value)) {
                $arrValue = [];
                foreach ($value as $key => $item) {
                    if ($item instanceof EntityInterface) {
                        $arrValue[$key] = $this->extract($item, $format);
                    } else {
                        $arrValue[$key] = $item;
                    }
                }
                $value = $arrValue;
            } elseif ($value instanceof EntityInterface) {
                $value = $this->extract($value, $format);
            }
            // 格式转化
            switch ($format) {
                case 'camel':
                    $name = StringHelper::camel($name);
                    break;
                case 'studly':
                    $name = StringHelper::studly($name);
                    break;
                default:
                    $name = StringHelper::snake($name);
                    break;
            }
            $result[$name] = $value;
        }

        return $result;
    }

    /**
     * 获取要转化对象的属性
     *
     * @param EntityInterface $entity
     * @return ReflectionProperty[]|mixed
     */
    protected function getReflectProperties(EntityInterface $entity)
    {
        $key = get_class($entity);
        if (isset(static::$reflectProperties[$key])) {
            return static::$reflectProperties[$key];
        }

        $reflectProperties = (new ReflectionClass($entity))->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($reflectProperties as $property) {
            $property->setAccessible(true);
            static::$reflectProperties[$key][$property->getName()] = $property;
        }

        return static::$reflectProperties[$key];
    }
}
