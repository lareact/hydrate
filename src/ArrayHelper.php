<?php

declare(strict_types=1);

namespace Golly\Hydrate;


use ArrayAccess;

/**
 * Class ArrayHelper
 * @package Golly\Hydrate
 */
class ArrayHelper
{
    /**
     * Determine whether the given value is array accessible.
     *
     * @param mixed $value
     * @return bool
     */
    public static function accessible(mixed $value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param array $array
     * @param int|string $key
     * @return bool
     */
    public static function exists(array $array, int|string $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param array $array
     * @param array $keys
     * @return void
     */
    public static function forget(array &$array, array $keys): void
    {
        $original = &$array;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param array $array
     * @param int|string $key
     * @param mixed|null $default
     * @return mixed
     */
    public static function get(array $array, int|string $key, mixed $default = null): mixed
    {
        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (!str_contains($key, '.')) {
            return $array[$key] ?? value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }

        return $array;
    }

    /**
     * Get all of the given array except for a specified array of keys.
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function except(array $array, array $keys): array
    {
        static::forget($array, $keys);

        return $array;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }
}
