<?php

declare(strict_types=1);

/**
 * This file is part of Esi\Utility.
 *
 * (c) 2017 - 2024 Eric Sizemore <admin@secondversion.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 */

namespace Esi\Utility;

use ArrayAccess;
use RuntimeException;
use SplObjectStorage;

use function array_map;
use function array_merge;
use function array_sum;

/**
 * Array utilities.
 *
 * @see Tests\ArraysTest
 *
 * @template TObject of SplObjectStorage
 * @template TData
 *
 * @uses SplObjectStorage<TObject, TData>
 */
abstract class Arrays
{
    /**
     * flatten().
     *
     * Flattens a multidimensional array.
     *
     * Keys are preserved based on $separator.
     *
     * @param array<TKey, TValue> $array     Array to flatten.
     * @param string              $separator The new keys are a list of original keys separated by $separator.
     * @param string              $prepend   A string to prepend to resulting array keys.
     *
     * @since 1.2.0
     *
     * @return array<TKey, TValue> The flattened array.
     *
     * @template TKey
     * @template TValue
     */
    public static function flatten(array $array, string $separator = '.', string $prepend = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (\is_array($value) && $value !== []) {
                $result = array_merge($result, Arrays::flatten($value, $separator, $prepend . $key . $separator));
            } else {
                $result[$prepend . $key] = $value;
            }
        }

        /**
         * @var array<TKey, TValue> $result
         */
        return $result;
    }

    /**
     * get().
     *
     * Retrieve a value from an array.
     *
     * @param array<TKey, TValue>|ArrayAccess<TKey, TValue> $array   Array to retrieve value from.
     * @param TKey                                          $key     Key to retrieve
     * @param TDefault                                      $default A default value to return if $key does not exist
     *
     * @throws RuntimeException If $array is not accessible
     *
     * @template TKey of (int|string)
     * @template TValue
     * @template TDefault
     *
     * @uses ArrayAccess<TKey, TValue>
     */
    public static function get(array|ArrayAccess $array, int|string $key, mixed $default = null): mixed
    {
        if (Arrays::keyExists($array, $key)) {
            return $array[$key];
        }

        return $default;
    }

    /**
     * Returns an associative array, grouped by $key, where the keys are the distinct values of $key,
     * and the values are arrays of items that share the same $key.
     *
     * *Important to note:* if a $key is provided that does not exist, the result will be an empty array.
     *
     * @since 2.0.0
     *
     * @param array<TKey, array<TKey, mixed>> $array Input array.
     * @param string                          $key   Key to use for grouping.
     *
     * @return array<mixed, non-empty-list<array<TKey, mixed>>>|array{}
     *
     * @template TKey
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

            $result[$groupKey] ??= [];
            $result[$groupKey][] = $item;
        }

        return $result;
    }

    /**
     * interlace().
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
     * @param array<TKey, TValue> ...$args
     *
     * @return array<TKey, TValue>|false|list<TValue>
     *
     * @template TKey
     * @template TValue
     */
    public static function interlace(array ...$args): array|false
    {
        $numArgs = \count($args);

        if ($numArgs === 0) {
            return false;
        }

        if ($numArgs === 1) {
            return $args[0];
        }

        $newArray      = [];
        $totalElements = array_sum(array_map('count', $args));

        for ($i = 0; $i < $totalElements; ++$i) {
            foreach ($args as $arg) {
                if (isset($arg[$i])) {
                    $newArray[] = $arg[$i];
                }
            }
        }

        return $newArray;
    }

    /**
     * isAssociative().
     *
     * Determines if the given array is an associative array.
     *
     * @param array<mixed> $array Array to check
     */
    public static function isAssociative(array $array): bool
    {
        return !array_is_list($array);
    }

    /**
     * Checks if a key exists in an array.
     *
     * @param array<TKey, TValue>|ArrayAccess<TKey, TValue> $array Array to check
     * @param TKey                                          $key   Key to check
     *
     * @return bool
     *
     * @template TKey of (int|string)
     * @template TValue
     *
     * @uses ArrayAccess<TKey, TValue>
     */
    public static function keyExists(array|ArrayAccess $array, int|string $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return \array_key_exists($key, $array);
    }

    /**
     * mapDeep().
     *
     * Recursively applies a callback to all non-iterable elements of an array or an object.
     *
     * @since 1.2.0 - updated with inspiration from the WordPress map_deep() function.
     * @see https://developer.wordpress.org/reference/functions/map_deep/
     *
     * @param array<mixed>|mixed|object $array    The array to apply $callback to.
     * @param callable                  $callback The callback function to apply.
     */
    public static function mapDeep(mixed $array, callable $callback): mixed
    {
        /**
         * @var ?SplObjectStorage<TObject, TData> $visited
         */
        static $visited;

        $visited ??= new SplObjectStorage();

        if (\is_object($array)) {
            if ($visited->contains($array)) {
                return $array; // Avoid circular references.
            }
            $visited->attach($array);
        }

        if (\is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = Arrays::mapDeep($value, $callback);
            }
        } elseif (\is_object($array)) {
            foreach (get_object_vars($array) as $key => $value) {
                $array->{$key} = Arrays::mapDeep($value, $callback);
            }
        } else {
            $array = $callback($array);
        }

        if (\is_object($array)) {
            $visited->detach($array);
        }

        return $array;
    }

    /**
     * set().
     *
     * Add a value to an array.
     *
     * @param array<TKey, TValue>|ArrayAccess<TKey, TValue> $array Array to add value to.
     * @param TKey                                          $key   Key to add
     * @param TValue                                        $value Value to add
     *
     * @param-out TValue|non-empty-array<TKey, TValue>|ArrayAccess<TKey, TValue> $array
     *
     * @throws RuntimeException If $array is not accessible
     *
     * @template TKey of (int|string)
     * @template TValue
     *
     * @uses ArrayAccess<TKey, TValue>
     */
    public static function set(array|ArrayAccess &$array, null|int|string $key, mixed $value): void
    {
        if ($key === null) {
            $array = $value;
        } else {
            if ($array instanceof ArrayAccess) {
                $array->offsetSet($key, $value);
            } else {
                $array[$key] = $value;
            }
        }
    }

    /**
     * Checks if a value exists in an array.
     *
     * @param array<TKey, TValue> $array Array to check
     * @param TKey                $value Value to check
     *
     * @template TKey of (int|string)
     * @template TValue
     */
    public static function valueExists(array $array, int|string $value): bool
    {
        return \in_array($value, $array, true);
    }
}
