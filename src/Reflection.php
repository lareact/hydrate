<?php

namespace Golly\Hydrate;

use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use Golly\Hydrate\Annotations\Mapping;
use Golly\Hydrate\Contracts\EntityInterface;
use Golly\Hydrate\Exceptions\InvalidArgumentException;
use Golly\Hydrate\Helpers\ArrHelper;
use Golly\Hydrate\Helpers\StrHelper;
use ReflectionClass;
use ReflectionException;
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
     * @param EntityInterface $object
     * @return EntityInterface
     */
    public function hydrate(array $data, EntityInterface $object)
    {
        try {
            $reflectProperties = $this->getReflectProperties($object);
            $annotationReader = new AnnotationReader();
            foreach ($reflectProperties as $name => $property) {
                $defaultValue = $property->getValue($object);
                $column = $annotationReader->getPropertyAnnotation($property, Mapping::class);
                if ($column instanceof Mapping) { // 映射关系
                    $value = ArrHelper::get($data, $column->field, $defaultValue);
                } else {
                    $value = ArrHelper::get($data, $name, $defaultValue);
                }
                $reflectProperties[$name]->setValue($object, $value);
            }
        } catch (Exception $e) {
            // TODO
        }

        return $object;
    }


    /**
     * 对象转为数组
     *
     * @param $object
     * @param string $format
     * @return array
     */
    public function extract($object, $format = 'snake')
    {
        $result = [];
        try {
            $properties = self::getReflectProperties($object);
            foreach ($properties as $name => $property) {
                $value = $property->getValue($object);
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
                        $name = StrHelper::camel($name);
                        break;
                    case 'studly':
                        $name = StrHelper::studly($name);
                        break;
                    default:
                        $name = StrHelper::snake($name);
                        break;
                }
                $result[$name] = $value;
            }
        } catch (Exception $e) {
            // TODO
        }

        return $result;
    }

    /**
     * 获取要转化对象的属性
     *
     * @param $input
     * @return ReflectionProperty[]
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    protected function getReflectProperties($input)
    {
        if (is_object($input)) {
            $key = get_class($input);
        } else {
            throw new InvalidArgumentException('Input must be an object.');
        }

        if (isset(static::$reflectProperties[$key])) {
            return static::$reflectProperties[$key];
        }

        $reflectProperties = (new ReflectionClass($input))->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($reflectProperties as $property) {
            $property->setAccessible(true);
            static::$reflectProperties[$key][$property->getName()] = $property;
        }

        return static::$reflectProperties[$key];
    }
}