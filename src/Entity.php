<?php
declare(strict_types=1);

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
    protected array $original = [];

    /**
     * @var array
     */
    protected array $array = [];

    /**
     * @var string
     */
    protected string $format = 'snake';

    /**
     * @param array $data
     * @param bool $original
     * @return static
     */
    public static function instance(array $data, bool $original = true): static
    {
        return (new static())->toObject($data, $original);
    }

    /**
     * array to this object
     *
     * @param array $data
     * @param bool $original
     * @return static
     */
    public function toObject(array $data, bool $original = true): static
    {
        if ($original) {
            $this->original = $data;
        }
        Reflection::hydrate($data, $this);

        return $this;
    }


    /**
     * @param callable|null $filter
     * @return array
     */
    public function toArray(callable $filter = null): array
    {
        if (!$this->array) {
            $this->array = Reflection::extract($this, $this->format);
        }
        if (is_null($filter)) {
            return $this->array;
        }

        return (array)$filter($this->array);
    }

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array
     */
    public function except(array $keys, callable $filter = null): array
    {
        return ArrayHelper::except($this->toArray($filter), $keys);
    }


    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array
     */
    public function only(array $keys, callable $filter = null): array
    {
        return ArrayHelper::only($this->toArray($filter), $keys);
    }


    /**
     * @param string|null $key
     * @return mixed
     */
    public function getOriginal(string $key = null): mixed
    {
        return ArrayHelper::get($this->original, $key);
    }
}
