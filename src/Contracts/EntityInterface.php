<?php
declare(strict_types=1);

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
    public static function instance(array $data): static;

    /**
     * @param array $data
     * @return static
     */
    public function toObject(array $data): static;

    /**
     * @param callable|null $filter
     * @return array
     */
    public function toArray(callable $filter = null): array;

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array
     */
    public function except(array $keys, callable $filter = null): array;

    /**
     * @param array $keys
     * @param callable|null $filter
     * @return array
     */
    public function only(array $keys, callable $filter = null): array;

    /**
     * @param string|null $key
     * @return mixed
     */
    public function getOriginal(string $key = null): mixed;

}
