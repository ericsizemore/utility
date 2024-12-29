<?php

declare(strict_types=1);

/**
 * This file is part of Esi\Utility.
 *
 * (c) 2017 - 2025 Eric Sizemore <admin@secondversion.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 */

namespace Esi\Utility;

use ArrayAccess;
use WeakMap;

/**
 * Array utilities.
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
     * @since 1.2.0
     *
     * @template TValue
     *
     * @param array<array-key, array<array-key, TValue>|TValue> $array
     *
     * @return array<string, TValue>
     */
    public static function flatten(array $array, string $separator = '.', string $prepend = ''): array
    {
        /** @var array<string, TValue> $result */
        $result = [];

        foreach ($array as $key => $value) {
            $currentKey = $prepend . $key;

            if (\is_array($value)) {
                $flattened = self::flatten($value, $separator, $currentKey . $separator);
                $result    = array_merge($result, $flattened);
                continue;
            }

            $result[$currentKey] = $value;
        }

        return $result;
    }

    /**
     * get().
     *
     * Retrieve a value from an array.
     *
     * @template TKey of array-key
     * @template TValue
     * @template TDefault
     *
     * @param array<TKey, TValue>|ArrayAccess<TKey, TValue> $array
     * @param TKey                                          $key
     * @param TDefault                                      $default
     *
     * @return TDefault|TValue
     */
    public static function get(array|ArrayAccess $array, int|string $key, mixed $default = null): mixed
    {
        if (self::keyExists($array, $key)) {
            /** @var TValue */
            return $array[$key];
        }

        /** @var TDefault */
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
     * @template TKey of array-key
     *
     * @param array<TKey, array{key?: mixed}> $array
     * @param non-empty-string                $key
     *
     * @return array<array-key, non-empty-list<array{key?: mixed}>>
     */
    public static function groupBy(array $array, string $key): array
    {
        /** @var array<array-key, non-empty-list<array{key?: mixed}>> $result */
        $result = [];

        foreach ($array as $item) {
            if (!isset($item[$key])) {
                continue;
            }

            /** @var array-key */
            $groupKey = $item[$key];

            if (!isset($result[$groupKey])) {
                $result[$groupKey] = [$item];
                continue;
            }

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
     * <code>
     *      var_dump(Utility\Arrays::interlace(
     *          [1, 2, 3],
     *          ['a', 'b', 'c']
     *      ));
     * </code>
     *
     * Result:
     * <code>
     *      Array (
     *          [0] => 1
     *          [1] => a
     *          [2] => 2
     *          [3] => b
     *          [4] => 3
     *          [5] => c
     *      )
     * </code>
     *
     * @since 1.2.0
     *
     * @template TValue
     *
     * @param array<array-key, TValue> ...$arrays
     *
     * @return array<int, TValue>|false
     */
    public static function interlace(array ...$arrays): array|false
    {
        if ($arrays === []) {
            return false;
        }

        if (\count($arrays) === 1) {
            /** @var array<int, TValue> */
            return array_values($arrays[0]);
        }

        /** @var array<int, TValue> $result */
        $result    = [];
        $maxLength = 0;

        foreach ($arrays as $array) {
            $maxLength = max($maxLength, \count($array));
        }

        for ($i = 0; $i < $maxLength; ++$i) {
            foreach ($arrays as $array) {
                if (isset($array[$i])) {
                    $result[] = $array[$i];
                }
            }
        }

        return $result;
    }

    /**
     * isAssociative().
     *
     * Determines if the given array is an associative array.
     *
     * @param array<array-key, mixed> $array
     */
    public static function isAssociative(array $array): bool
    {
        if ($array === []) {
            return false;
        }

        return array_keys($array) !== range(0, \count($array) - 1);
    }

    /**
     * Checks if a key exists in an array.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param array<TKey, TValue>|ArrayAccess<TKey, TValue> $array
     * @param TKey                                          $key
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
     * @since 1.2.0
     *
     * @template TValue
     *
     * @param mixed                      $data     Data to process
     * @param callable(mixed): TValue    $callback Callback to apply to non-iterable values
     * @param null|WeakMap<object, true> $seen     Track objects to prevent circular reference issues
     */
    public static function mapDeep(mixed $data, callable $callback, ?WeakMap $seen = null): mixed
    {
        /** @var WeakMap<object, true> $weakMap */
        $weakMap = $seen ?? (static function (): WeakMap {
            /** @return WeakMap<object, true> */
            return new WeakMap();
        })();

        if (!\is_array($data) && !\is_object($data)) {
            return $callback($data);
        }

        if (\is_array($data)) {
            return array_map(
                static fn (mixed $item): mixed => self::mapDeep($item, $callback, $weakMap),
                $data
            );
        }

        if ($weakMap->offsetExists($data)) {
            return $data;
        }

        $weakMap->offsetSet($data, true);

        try {
            $props = get_object_vars($data);

            array_walk(
                $props,
                static function (mixed $propValue, string $propName) use ($data, $callback, $weakMap): void {
                    $data->$propName = self::mapDeep($propValue, $callback, $weakMap);
                }
            );
        } finally {
            $weakMap->offsetUnset($data);
        }

        return $data;
    }

    /**
     * set().
     *
     * Add a value to an array.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param array<TKey, TValue>|ArrayAccess<TKey, TValue> $array
     * @param null|TKey                                     $key
     * @param TValue                                        $value
     *
     * @param-out (TValue|array<TKey, TValue>|ArrayAccess<TKey, TValue>) $array
     */
    public static function set(array|ArrayAccess &$array, null|int|string $key, mixed $value): void
    {
        if ($key === null) {
            $array = $value;
            return;
        }

        if ($array instanceof ArrayAccess) {
            $array->offsetSet($key, $value);
            return;
        }

        $array[$key] = $value;
    }

    /**
     * Checks if a value exists in an array.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param array<TKey, TValue> $array
     * @param TValue              $value
     */
    public static function valueExists(array $array, mixed $value): bool
    {
        return \in_array($value, $array, true);
    }
}
