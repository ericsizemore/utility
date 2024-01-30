<?php

declare(strict_types=1);

/**
 * Utility - Collection of various PHP utility functions.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @version   2.0.0
 * @copyright (C) 2017 - 2024 Eric Sizemore
 * @license   The MIT License (MIT)
 *
 * Copyright (C) 2017 - 2024 Eric Sizemore <https://www.secondversion.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Esi\Utility;

// Exceptions
use RuntimeException;

// Classes
use ArrayAccess;

// Functions
use function is_array;
use function is_null;
use function array_key_exists;
use function array_merge;
use function is_object;
use function get_object_vars;
use function call_user_func;
use function count;
use function array_sum;
use function array_map;

/**
 * Array utilities.
 */
final class Arrays
{
    /**
     * isAssociative()
     *
     * Determines if the given array is an associative array.
     *
     * @param  array<mixed>  $array  Array to check
     */
    public static function isAssociative(array $array): bool
    {
        return !array_is_list($array);
    }

    /**
     * get()
     *
     * Retrieve a value from an array.
     *
     * @param  array<mixed>|ArrayAccess<mixed, mixed>  $array    Array to retrieve value from.
     * @param  string|int    $key      Key to retrieve
     * @param  mixed         $default  A default value to return if $key does not exist
     *
     * @throws RuntimeException  If $array is not accessible
     */
    public static function get(array | ArrayAccess $array, string | int $key, mixed $default = null): mixed
    {
        if (Arrays::exists($array, $key)) {
            return $array[$key];
        }
        return $default;
    }

    /**
     * set()
     *
     * Add a value to an array.
     *
     * @param  array<mixed>|ArrayAccess<mixed, mixed>     &$array  Array to add value to.
     * @param  string|int|null  $key     Key to add
     * @param  mixed            $value   Value to add
     *
     * @throws RuntimeException  If $array is not accessible
     */
    public static function set(array | ArrayAccess &$array, string | int | null $key, mixed $value): mixed
    {
        if (is_null($key)) {
            return $array = $value;
        }
        return $array[$key] = $value;
    }

    /**
     * Checks if a key exists in an array.
     *
     * @param  array<mixed>|ArrayAccess<mixed, mixed>  $array  Array to check
     * @param  string|int                $key    Key to check
     */
    public static function exists(array | ArrayAccess $array, string | int $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }
        return ((array_key_exists($key, $array)) || (in_array($key, $array, true)));
    }

    /**
     * flatten()
     *
     * Flattens a multidimensional array.
     *
     * Keys are preserved based on $separator.
     *
     * @param   array<mixed>   $array      Array to flatten.
     * @param   string         $separator  The new keys are a list of original keys separated by $separator.
     *
     * @since 1.2.0
     * @param   string         $prepend    A string to prepend to resulting array keys.
     *
     * @return  array<mixed>               The flattened array.
     */
    public static function flatten(array $array, string $separator = '.', string $prepend = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && $value !== []) {
                $result = array_merge($result, Arrays::flatten($value, $separator, $prepend . $key . $separator));
            } else {
                $result[$prepend . $key] = $value;
            }
        }
        return $result;
    }

    /**
     * mapDeep()
     *
     * Recursively applies a callback to all non-iterable elements of an array or an object.
     *
     * @since 1.2.0 - updated with inspiration from the WordPress map_deep() function.
     *      @see https://developer.wordpress.org/reference/functions/map_deep/
     *
     * @param   mixed     $array     The array to apply $callback to.
     * @param   callable  $callback  The callback function to apply.
     */
    public static function mapDeep(mixed $array, callable $callback): mixed
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = Arrays::mapDeep($value, $callback);
            }
        } elseif (is_object($array)) {
            foreach (get_object_vars($array) as $key => $value) {
                // @phpstan-ignore-next-line
                $array->$key = Arrays::mapDeep($value, $callback);
            }
        } else {
            $array = call_user_func($callback, $array);
        }
        return $array;
    }

    /**
     * interlace()
     *
     * Interlaces one or more arrays' values (not preserving keys).
     *
     * Example:
     *
     *      var_dump(Utility\Arrays::interlace(
     *          [1, 2, 3],
     *          ['a', 'b', 'c']
     *      ));
     *
     * Result:
     *      Array (
     *          [0] => 1
     *          [1] => a
     *          [2] => 2
     *          [3] => b
     *          [4] => 3
     *          [5] => c
     *      )
     *
     * @since 1.2.0
     *
     * @param  array<mixed>        ...$args
     * @return array<mixed>|false
     */
    public static function interlace(array ...$args): array | false
    {
        $numArgs = count($args);

        if ($numArgs === 0) {
            return false;
        }

        if ($numArgs === 1) {
            return $args[0];
        }

        $newArray      = [];
        $totalElements = array_sum(array_map('count', $args));

        for ($i = 0; $i < $totalElements; $i++) {
            foreach ($args as $arg) {
                if (isset($arg[$i])) {
                    $newArray[] = $arg[$i];
                }
            }
        }
        return $newArray;
    }

    /**
     *
     * Returns an associative array, grouped by $key, where the keys are the distinct values of $key,
     * and the values are arrays of items that share the same $key.
     *
     * *Important to note:* if a $key is provided that does not exist, the result will be an empty array.
     *
     * @since 2.0.0
     *
     * @param array<mixed, array<mixed>>  $array  Input array.
     * @param string                      $key    Key to use for grouping.
     * @return array<mixed, array<mixed>>
     */
    public static function groupBy(array $array, string $key): array
    {
        $result = [];

        foreach ($array as $item) {
            if (!self::isAssociative($item)) {
                //@codeCoverageIgnoreStart
                continue;
                //@codeCoverageIgnoreEnd
            }

            if (!isset($item[$key])) {
                continue;
            }

            $groupKey = $item[$key];

            if (!isset($result[$groupKey])) {
                $result[$groupKey] = [];
            }

            $result[$groupKey][] = $item;
        }
        return $result;
    }
}
