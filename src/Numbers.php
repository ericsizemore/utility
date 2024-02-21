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
use InvalidArgumentException;
use Random\RandomException;
use ValueError;

// Functions
use function abs;
use function count;
use function number_format;
use function random_int;
use function sprintf;

/**
 * Number utilities.
 * @see \Esi\Utility\Tests\NumbersTest
 */
final class Numbers
{
    /**
     * Constants for Numbers::sizeFormat(). Sets bases and modifier for the conversion.
     *
     * @var int   BINARY_STANDARD_BASE
     * @var int   METRIC_STANDARD_BASE
     * @var float CONVERSION_MODIFIER
     */
    public const BINARY_STANDARD_BASE = 1_024;
    public const METRIC_STANDARD_BASE = 1_000;
    public const CONVERSION_MODIFIER  = 0.9;

    /**
     * Ordinal suffixes.
     *
     * @var array<string> SUFFIXES
     */
    public const SUFFIXES = ['th', 'st', 'nd', 'rd'];

    /**
     * Standards units.
     *
     * @var array<string, array<string>> SIZE_FORMAT_UNITS
     */
    public const SIZE_FORMAT_UNITS = [
        'binary' => ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'],
        'metric' => ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
    ];

    /**
     * inside()
     *
     * Determines if a number is inside the min and max.
     *
     * @param  float|int  $number  The number to check.
     * @param  float|int  $min     The minimum.
     * @param  float|int  $max     The maximum.
     */
    public static function inside(float | int $number, float | int $min, float | int $max): bool
    {
        return ($number >= $min && $number <= $max);
    }

    /**
     * outside()
     *
     * Determines if a number is outside the min and max.
     *
     * @param  float|int  $number  The number to check.
     * @param  float|int  $min     The minimum.
     * @param  float|int  $max     The maximum.
     */
    public static function outside(float | int $number, float | int $min, float | int $max): bool
    {
        return ($number < $min || $number > $max);
    }

    /**
     * random()
     *
     * Generate a cryptographically secure pseudo-random integer.
     *
     * @param   int<min, max>  $min  The lowest value to be returned, which must be PHP_INT_MIN or higher.
     * @param   int<min, max>  $max  The highest value to be returned, which must be less than or equal to PHP_INT_MAX.
     * @return  int<min, max>
     *
     * @throws RandomException | ValueError
     */
    public static function random(int $min, int $max): int
    {
        // Generate random int
        return random_int($min, $max);
    }

    /**
     * ordinal()
     *
     * Retrieve the ordinal version of a number.
     *
     * Basically, it will append th, st, nd, or rd based on what the number ends with.
     *
     * @param  int  $number  The number to create an ordinal version of.
     */
    public static function ordinal(int $number): string
    {
        static $suffixes = self::SUFFIXES;

        $absNumber = abs($number);

        $suffix = ($absNumber % 100 >= 11 && $absNumber % 100 <= 13)
                  ? $suffixes[0]
                  : $suffixes[$absNumber % 10] ?? $suffixes[0];

        return $number . $suffix;
    }

    /**
     * sizeFormat()
     *
     * Format bytes to a human-readable format.
     *
     * @param  int     $bytes      The number in bytes.
     * @param  int     $precision  Sets the number of decimal digits.
     * @param  string  $standard   Determines which mod ('base') to use in the conversion.
     */
    public static function sizeFormat(int $bytes, int $precision = 0, string $standard = 'binary'): string
    {
        // The units/labels for each 'system'
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
            return sprintf('%s %s', $bytes, $units[0]);
        }

        // Perform the conversion
        for ($i = 0; ($bytes / $base) > Numbers::CONVERSION_MODIFIER && ($i < count($units) - 1); $i++) {
            $bytes /= $base;
        }
        // @phpstan-ignore-next-line
        return number_format($bytes, $precision, '.', '') . ' ' . $units[$i];
    }
}
