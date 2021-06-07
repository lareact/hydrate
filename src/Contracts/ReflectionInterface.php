<?php

namespace Golly\Hydrate\Contracts;

/**
 * Interface ReflectionInterface
 * @package Golly\Hydrate\Contracts
 */
interface ReflectionInterface
{

    public static function hydrate(array $data, EntityInterface $entity): EntityInterface;

    public static function extract(EntityInterface $entity, string $format = 'snake'): array;


}
