<?php

declare(strict_types=1);

namespace Golly\Hydrate;


/**
 * Trait StringHelper
 * @package Golly\Hydrate
 */
class StringHelper
{
    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static array $snakes = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static array $camels = [];

    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static array $studlies = [];


    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     * @return string
     */
    public static function studly(string $value): string
    {
        $key = $value;

        if (isset(static::$studlies[$key])) {
            return static::$studlies[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlies[$key] = str_replace(' ', '', $value);
    }

    /**
     * Convert a value to camel case.
     *
     * @param string $value
     * @return string
     */
    public static function camel(string $value): string
    {
        if (isset(static::$camels[$value])) {
            return static::$camels[$value];
        }

        return static::$camels[$value] = lcfirst(static::studly($value));
    }


    /**
     * Convert the given string to lower-case.
     *
     * @param string $value
     * @return string
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Convert a string to snake case.
     *
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        $key = $value;
        if (isset(static::$snakes[$key][$delimiter])) {
            return static::$snakes[$key][$delimiter];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return static::$snakes[$key][$delimiter] = $value;
    }
}
