<?php

namespace Golly\Hydrate\Contracts;

/**
 * Interface EntityInterface
 * @package Golly\Hydrate\Contracts
 */
interface EntityInterface
{

    /**
     * @param array $data
     * @return static
     */
    public static function instance(array $data);

    /**
     * @param array $data
     * @return static
     */
    public function toObject(array $data);

    /**
     * @param callable|null $filter
     * @return mixed
     */
    public function toArray(callable $filter = null);

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return mixed
     */
    public function except(array $keys, callable $filter = null);

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return mixed
     */
    public function only(array $keys, callable $filter = null);

    /**
     * @param string|null $key
     * @return array|string
     */
    public function getOriginal($key = null);

}