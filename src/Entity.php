<?php

namespace Golly\Hydrate;

use Golly\Hydrate\Contracts\EntityInterface;


/**
 * Class Entity
 * @package Golly\Hydrate
 */
class Entity implements EntityInterface
{
    /**
     * @var array
     */
    protected $original = [];

    /**
     * @var array
     */
    protected $array = [];

    /**
     * @var string
     */
    protected $format = 'snake';

    /**
     * @param array $data
     * @param bool $original
     * @return EntityInterface|static
     */
    public static function instance(array $data, $original = true)
    {
        return (new static())->toObject($data, $original);
    }

    /**
     * array to this object
     *
     * @param array $data
     * @param bool $original
     * @return EntityInterface|static
     */
    public function toObject(array $data, $original = true)
    {
        if ($original) {
            $this->original = $data;
        }

        return $this->newReflection()->hydrate($data, $this);
    }


    /**
     * @param callable|null $filter
     * @return array
     */
    public function toArray(callable $filter = null)
    {
        if (!$this->array) {
            $this->array = $this->newReflection()->extract($this, $this->format);
        }
        if (is_null($filter)) {
            return $this->array;
        }

        return $filter($this->array);
    }

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array
     */
    public function except(array $keys, callable $filter = null)
    {
        return ArrayHelper::except($this->toArray($filter), $keys);
    }


    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array
     */
    public function only(array $keys, callable $filter = null)
    {
        return ArrayHelper::only($this->toArray($filter), $keys);
    }


    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getOriginal($key = null, $default = null)
    {
        return ArrayHelper::get($this->original, $key, $default);
    }

    /**
     * @return Reflection
     */
    protected function newReflection()
    {
        return new Reflection();
    }
}
