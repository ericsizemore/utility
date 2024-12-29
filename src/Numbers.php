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

use InvalidArgumentException;
use Random\RandomException;

use function abs;
use function number_format;
use function random_int;

/**
 * Number utilities.
 *
 * @see Tests\NumbersTest
 */
abstract class Numbers
{
    /**
     * Constants for Numbers::sizeFormat(). Sets bases and modifier for the conversion.
     *
     * @var int   BINARY_STANDARD_BASE
     * @var int   METRIC_STANDARD_BASE
     * @var float CONVERSION_MODIFIER
     */
    public const BINARY_STANDARD_BASE = 1_024;

    public const CONVERSION_MODIFIER = 0.9;

    public const METRIC_STANDARD_BASE = 1_000;

    /**
     * Standards units.
     *
     * @var array{
     *     binary: array<string>,
     *     metric: array<string>
     * } SIZE_FORMAT_UNITS
     */
    public const SIZE_FORMAT_UNITS = [
        'binary' => ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'],
        'metric' => ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
    ];

    /**
     * Ordinal suffixes.
     *
     * @var string[] SUFFIXES
     */
    public const SUFFIXES = ['th', 'st', 'nd', 'rd'];

    /**
     * inside().
     *
     * Determines if a number is inside the min and max.
     *
     * @param float|int $number The number to check.
     * @param float|int $min    The minimum.
     * @param float|int $max    The maximum.
     */
    public static function inside(float|int $number, float|int $min, float|int $max): bool
    {
        return ($number >= $min && $number <= $max);
    }

    /**
     * ordinal().
     *
     * Retrieve the ordinal version of a number.
     *
     * Basically, it will append th, st, nd, or rd based on what the number ends with.
     *
     * @param int $number The number to create an ordinal version of.
     */
    public static function ordinal(int $number): string
    {
        $suffixes = self::SUFFIXES;

        $absNumber = abs($number);

        /**
         * @var string $suffix
         *
         * @psalm-suppress InvalidArrayOffset
         */
        $suffix = ($absNumber % 100 >= 11 && $absNumber % 100 <= 13)
                  ? $suffixes[0]
                  : $suffixes[$absNumber % 10] ?? $suffixes[0];

        return $number . $suffix;
    }

    /**
     * outside().
     *
     * Determines if a number is outside the min and max.
     *
     * @param float|int $number The number to check.
     * @param float|int $min    The minimum.
     * @param float|int $max    The maximum.
     */
    public static function outside(float|int $number, float|int $min, float|int $max): bool
    {
        return ($number < $min || $number > $max);
    }

    /**
     * random().
     *
     * Generate a cryptographically secure pseudo-random integer.
     *
     * @param int<min, max> $min The lowest value to be returned, which must be PHP_INT_MIN or higher.
     * @param int<min, max> $max The highest value to be returned, which must be less than or equal to PHP_INT_MAX.
     *
     * @throws RandomException
     *
     * @return int<min, max>
     */
    public static function random(int $min, int $max): int
    {
        // Generate random int
        return random_int($min, $max);
    }

    /**
     * sizeFormat().
     *
     * Format bytes to a human-readable format.
     *
     * @param int    $bytes     The number in bytes.
     * @param int    $precision Sets the number of decimal digits.
     * @param string $standard  Determines which mod ('base') to use in the conversion.
     */
    public static function sizeFormat(int $bytes, int $precision = 0, string $standard = 'binary'): string
    {
        /**
         * The units/labels for each 'system'.
         *
         * @var array{
         *     binary: array{base: int, units: array<string>},
         *     metric: array{base: int, units: array<string>},
         * } $standards
         */
        static $standards = [
            'binary' => ['base' => Numbers::BINARY_STANDARD_BASE, 'units' => self::SIZE_FORMAT_UNITS['binary']],
            'metric' => ['base' => Numbers::METRIC_STANDARD_BASE, 'units' => self::SIZE_FORMAT_UNITS['metric']],
        ];

        // Just a sanity check
        if (!isset($standards[$standard])) {
            throw new InvalidArgumentException('Invalid $standard specified, must be either metric or binary');
        }

        // Metric or Binary?
        $base  = $standards[$standard]['base'];
        $units = $standards[$standard]['units'];

        // If $bytes is less than our base, there is no need for any conversion
        if ($bytes < $base) {
            return \sprintf('%s %s', $bytes, $units[0]);
        }

        // Perform the conversion
        for ($i = 0; ($bytes / $base) > Numbers::CONVERSION_MODIFIER && ($i < \count($units) - 1); ++$i) {
            /**
             * @var float $bytes
             */
            $bytes /= $base;
        }

        return number_format($bytes, $precision, '.', '') . ' ' . $units[$i];
    }
}
