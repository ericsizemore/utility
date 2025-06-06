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

use function atan2;
use function cos;
use function deg2rad;
use function number_format;
use function round;
use function sin;
use function sqrt;

/**
 * Conversion utilities.
 *
 * @see Tests\ConversionTest
 */
abstract class Conversion
{
    /**
     * @const int EARTH_RADIUS          Earth's radius, in meters.
     * @const int METERS_TO_KILOMETERS  Used in the conversion of meters to kilometers.
     * @const int METERS_TO_MILES       Used in the conversion of meters to miles.
     */
    public const EARTH_RADIUS = 6_370_986;

    public const METERS_TO_KILOMETERS = 1_000;

    public const METERS_TO_MILES = 1_609.344;

    /**
     * @todo The temperature conversion functions are approximate, loses some accuracy.
     */

    /**
     * Convert Celsius (Cº) To Fahrenheit (Fº).
     *
     * @since  1.2.0
     *
     * @param float $celsius   Value in Celsius
     * @param bool  $rounded   Whether to round the result.
     * @param int   $precision Precision to use if $rounded is true.
     */
    public static function celsiusToFahrenheit(float $celsius, bool $rounded = true, int $precision = 2): float
    {
        $result = ($celsius * 1.8) + 32.0;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Celsius (Cº) To Kelvin (K).
     *
     * @since  1.2.0
     *
     * @param float $celsius   Value in Celsius
     * @param bool  $rounded   Whether to round the result.
     * @param int   $precision Precision to use if $rounded is true.
     */
    public static function celsiusToKelvin(float $celsius, bool $rounded = true, int $precision = 2): float
    {
        $result = $celsius + 273.15;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Celsius (Cº) To Rankine (ºR).
     *
     * @since  1.2.0
     *
     * @param float $celsius   Value in Celsius
     * @param bool  $rounded   Whether to round the result.
     * @param int   $precision Precision to use if $rounded is true.
     */
    public static function celsiusToRankine(float $celsius, bool $rounded = true, int $precision = 2): float
    {
        $result = ($celsius * 1.8) + 491.67;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Fahrenheit (Fº) To Celsius (Cº).
     *
     * @since  1.2.0
     *
     * @param float $fahrenheit Value in Fahrenheit
     * @param bool  $rounded    Whether to round the result.
     * @param int   $precision  Precision to use if $rounded is true.
     */
    public static function fahrenheitToCelsius(float $fahrenheit, bool $rounded = true, int $precision = 2): float
    {
        $result = ($fahrenheit - 32.0) / 1.8;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Fahrenheit (Fº) To Kelvin (K).
     *
     * @since  1.2.0
     *
     * @param float $fahrenheit Value in Fahrenheit
     * @param bool  $rounded    Whether to round the result.
     * @param int   $precision  Precision to use if $rounded is true.
     */
    public static function fahrenheitToKelvin(float $fahrenheit, bool $rounded = true, int $precision = 2): float
    {
        $result = (($fahrenheit - 32.0) / 1.8) + 273.15;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Fahrenheit (Fº) To Rankine (ºR).
     *
     * @since  1.2.0
     *
     * @param float $fahrenheit Value in Fahrenheit
     * @param bool  $rounded    Whether to round the result.
     * @param int   $precision  Precision to use if $rounded is true.
     */
    public static function fahrenheitToRankine(float $fahrenheit, bool $rounded = true, int $precision = 2): float
    {
        $result = $fahrenheit + 459.67;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Calculate the distance between two points using the Haversine Formula.
     *
     * While I've made every effort to implement this formula as accurately as possible, there is a
     * chance for some discrepancies.
     *
     * For a much better explanation of the formula than I can provide:
     *
     * @see https://en.wikipedia.org/wiki/Haversine_formula#Formulation
     * @since 2.0.0
     *
     * @param float|int $startingLatitude  The latitude of the first point.
     * @param float|int $startingLongitude The longitude of the first point.
     * @param float|int $endingLatitude    The latitude of the second point.
     * @param float|int $endingLongitude   The longitude of the second point.
     * @param int       $precision         Sets the number of decimal digits.
     *
     * @return array<string, float|int|string>
     */
    public static function haversineDistance(
        float|int $startingLatitude,
        float|int $startingLongitude,
        float|int $endingLatitude,
        float|int $endingLongitude,
        int $precision = 0
    ): array {
        if (!\is_float($startingLatitude)) {
            $startingLatitude = (float) $startingLatitude;
        }

        if (!\is_float($endingLatitude)) {
            $endingLatitude = (float) $endingLatitude;
        }

        if (!\is_float($startingLongitude)) {
            $startingLongitude = (float) $startingLongitude;
        }

        if (!\is_float($endingLongitude)) {
            $endingLongitude = (float) $endingLongitude;
        }

        // Pre-calculate values used multiple times
        $startingLatRad = deg2rad($startingLatitude);
        $endingLatRad   = deg2rad($endingLatitude);
        $latDiff        = deg2rad($endingLatitude - $startingLatitude);
        $lonDiff        = deg2rad($endingLongitude - $startingLongitude);

        $cosStartLat = cos($startingLatRad);
        $cosEndLat   = cos($endingLatRad);

        // Calculate using optimized formula
        $sinLatHalf = sin($latDiff / 2.0);
        $sinLonHalf = sin($lonDiff / 2.0);

        $square = $sinLatHalf * $sinLatHalf +
            $cosStartLat * $cosEndLat *
            $sinLonHalf * $sinLonHalf;

        $distance = (float) self::EARTH_RADIUS * 2.0 * atan2(sqrt($square), sqrt(1.0 - $square));

        return [
            'meters'     => number_format($distance, $precision),
            'kilometers' => number_format($distance / (float) self::METERS_TO_KILOMETERS, $precision),
            'miles'      => number_format($distance / self::METERS_TO_MILES, $precision),
        ];
    }

    /**
     * Convert Kelvin (K) To Celsius (Cº).
     *
     * @since  1.2.0
     *
     * @param float $kelvin    Value in Kelvin
     * @param bool  $rounded   Whether to round the result.
     * @param int   $precision Precision to use if $rounded is true.
     */
    public static function kelvinToCelsius(float $kelvin, bool $rounded = true, int $precision = 2): float
    {
        $result = $kelvin - 273.15;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Kelvin (K) To Fahrenheit (Fº).
     *
     * @since  1.2.0
     *
     * @param float $kelvin    Value in Kelvin
     * @param bool  $rounded   Whether to round the result.
     * @param int   $precision Precision to use if $rounded is true.
     */
    public static function kelvinToFahrenheit(float $kelvin, bool $rounded = true, int $precision = 2): float
    {
        $result = (($kelvin - 273.15) * 1.8) + 32.0;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Kelvin (K) To Rankine (ºR).
     *
     * @since  1.2.0
     *
     * @param float $kelvin    Value in Kelvin
     * @param bool  $rounded   Whether to round the result.
     * @param int   $precision Precision to use if $rounded is true.
     */
    public static function kelvinToRankine(float $kelvin, bool $rounded = true, int $precision = 2): float
    {
        $result = (($kelvin - 273.15) * 1.8) + 491.67;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Rankine (ºR) To Celsius (Cº).
     *
     * @since  1.2.0
     *
     * @param float $rankine   Value in Rankine
     * @param bool  $rounded   Whether to round the result.
     * @param int   $precision Precision to use if $rounded is true.
     */
    public static function rankineToCelsius(float $rankine, bool $rounded = true, int $precision = 2): float
    {
        $result = ($rankine - 491.67) / 1.8;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Rankine (ºR) To Fahrenheit (Fº).
     *
     * @since  1.2.0
     *
     * @param float $rankine   Value in Rankine
     * @param bool  $rounded   Whether to round the result.
     * @param int   $precision Precision to use if $rounded is true.
     */
    public static function rankineToFahrenheit(float $rankine, bool $rounded = true, int $precision = 2): float
    {
        $result = $rankine - 459.67;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Rankine (ºR) To Kelvin (K).
     *
     * @since  1.2.0
     *
     * @param float $rankine   Value in Rankine
     * @param bool  $rounded   Whether to round the result.
     * @param int   $precision Precision to use if $rounded is true.
     */
    public static function rankineToKelvin(float $rankine, bool $rounded = true, int $precision = 2): float
    {
        $result = (($rankine - 491.67) / 1.8) + 273.15;

        return ($rounded) ? round($result, $precision) : $result;
    }
}
