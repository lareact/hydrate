<?php

namespace Golly\Hydrate;

use Golly\Hydrate\Contracts\EntityInterface;
use Golly\Hydrate\Helpers\ArrHelper;


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
     * @return EntityInterface|static
     */
    public static function instance(array $data)
    {
        return (new static())->toObject($data);
    }

    /**
     * array to this object
     *
     * @param array $data
     * @return EntityInterface|static
     */
    public function toObject(array $data)
    {
        $this->original = $data;

        return $this->getReflection()->hydrate($data, $this);
    }


    /**
     * @param callable|null $filter
     * @return array
     */
    public function toArray(callable $filter = null)
    {
        if (!$this->array) {
            $this->array = $this->getReflection()->extract($this, $this->format);
        }
        if (is_null($filter)) {
            return $this->array;
        }

        return $filter($this->array);
    }

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array|mixed
     */
    public function except(array $keys, callable $filter = null)
    {
        return ArrHelper::except($this->toArray($filter), $keys);
    }


    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array|mixed
     */
    public function only(array $keys, callable $filter = null)
    {
        return ArrHelper::only($this->toArray($filter), $keys);
    }


    /**
     * @param null $key
     * @param null $default
     * @return array|string
     */
    public function getOriginal($key = null, $default = null)
    {
        if ($key) {
            return ArrHelper::get($this->original, $key, $default);
        }

        return $this->original;
    }

    /**
     * @return Reflection
     */
    protected function getReflection()
    {
        return app(Reflection::class);
    }
}