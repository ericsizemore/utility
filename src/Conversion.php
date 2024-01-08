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

use Random\RandomException;
use ValueError;

// Functions
use function round;
use function number_format;
use function abs;
use function atan;
use function tan;
use function deg2rad;
use function sin;
use function cos;
use function sqrt;
use function atan2;
use function is_nan;

use const NAN;

/**
 * Conversion utilities.
 */
final class Conversion
{
    /**
     * Convert Fahrenheit (Fº) To Celsius (Cº)
     *
     * @since  1.2.0
     *
     * @param   float  $fahrenheit  Value in Fahrenheit
     * @param   bool   $rounded     Whether to round the result.
     * @param   int    $precision   Precision to use if $rounded is true.
     * @return  float
     */
    public static function fahrenheitToCelsius(float $fahrenheit, bool $rounded = true, int $precision = 2): float
    {
        $result = ($fahrenheit - 32) / 1.8;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Celsius (Cº) To Fahrenheit (Fº)
     *
     * @since  1.2.0
     *
     * @param   float  $celsius    Value in Celsius
     * @param   bool   $rounded    Whether to round the result.
     * @param   int    $precision  Precision to use if $rounded is true.
     * @return  float
     */
    public static function celsiusToFahrenheit(float $celsius, bool $rounded = true, int $precision = 2): float
    {
        $result = ($celsius * 1.8) + 32;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Celsius (Cº) To Kelvin (K)
     *
     * @since  1.2.0
     *
     * @param   float  $celsius    Value in Celsius
     * @param   bool   $rounded    Whether to round the result.
     * @param   int    $precision  Precision to use if $rounded is true.
     * @return  float
     */
    public static function celsiusToKelvin(float $celsius, bool $rounded = true, int $precision = 2): float
    {
        $result = $celsius + 273.15;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Kelvin (K) To Celsius (Cº)
     *
     * @since  1.2.0
     *
     * @param   float  $kelvin     Value in Kelvin
     * @param   bool   $rounded    Whether to round the result.
     * @param   int    $precision  Precision to use if $rounded is true.
     * @return  float
     */
    public static function kelvinToCelsius(float $kelvin, bool $rounded = true, int $precision = 2): float
    {
        $result = $kelvin - 273.15;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Fahrenheit (Fº) To Kelvin (K)
     *
     * @since  1.2.0
     *
     * @param   float  $fahrenheit  Value in Fahrenheit
     * @param   bool   $rounded     Whether to round the result.
     * @param   int    $precision   Precision to use if $rounded is true.
     * @return  float
     */
    public static function fahrenheitToKelvin(float $fahrenheit, bool $rounded = true, int $precision = 2): float
    {
        $result = (($fahrenheit - 32) / 1.8) + 273.15;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Kelvin (K) To Fahrenheit (Fº)
     *
     * @since  1.2.0
     *
     * @param   float  $kelvin     Value in Kelvin
     * @param   bool   $rounded    Whether to round the result.
     * @param   int    $precision  Precision to use if $rounded is true.
     * @return  float
     */
    public static function kelvinToFahrenheit(float $kelvin, bool $rounded = true, int $precision = 2): float
    {
        $result = (($kelvin - 273.15) * 1.8) + 32;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Fahrenheit (Fº) To Rankine (ºR)
     *
     * @since  1.2.0
     *
     * @param   float  $fahrenheit  Value in Fahrenheit
     * @param   bool   $rounded     Whether to round the result.
     * @param   int    $precision   Precision to use if $rounded is true.
     * @return  float
     */
    public static function fahrenheitToRankine(float $fahrenheit, bool $rounded = true, int $precision = 2): float
    {
        $result = $fahrenheit + 459.67;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Rankine (ºR) To Fahrenheit (Fº)
     *
     * @since  1.2.0
     *
     * @param   float  $rankine    Value in Rankine
     * @param   bool   $rounded    Whether to round the result.
     * @param   int    $precision  Precision to use if $rounded is true.
     * @return  float
     */
    public static function rankineToFahrenheit(float $rankine, bool $rounded = true, int $precision = 2): float
    {
        $result = $rankine - 459.67;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Celsius (Cº) To Rankine (ºR)
     *
     * @since  1.2.0
     *
     * @param   float  $celsius    Value in Celsius
     * @param   bool   $rounded    Whether to round the result.
     * @param   int    $precision  Precision to use if $rounded is true.
     * @return  float
     */
    public static function celsiusToRankine(float $celsius, bool $rounded = true, int $precision = 2): float
    {
        $result = ($celsius * 1.8) + 491.67;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Rankine (ºR) To Celsius (Cº)
     *
     * @since  1.2.0
     *
     * @param   float  $rankine    Value in Rankine
     * @param   bool   $rounded    Whether to round the result.
     * @param   int    $precision  Precision to use if $rounded is true.
     * @return  float
     */
    public static function rankineToCelsius(float $rankine, bool $rounded = true, int $precision = 2): float
    {
        $result = ($rankine - 491.67) / 1.8;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Kelvin (K) To Rankine (ºR)
     *
     * @since  1.2.0
     *
     * @param   float  $kelvin     Value in Kelvin
     * @param   bool   $rounded    Whether to round the result.
     * @param   int    $precision  Precision to use if $rounded is true.
     * @return  float
     */
    public static function kelvinToRankine(float $kelvin, bool $rounded = true, int $precision = 2): float
    {
        $result = (($kelvin - 273.15) * 1.8) + 491.67;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Rankine (ºR) To Kelvin (K)
     *
     * @since  1.2.0
     *
     * @param   float  $rankine    Value in Rankine
     * @param   bool   $rounded    Whether to round the result.
     * @param   int    $precision  Precision to use if $rounded is true.
     * @return  float
     */
    public static function rankineToKelvin(float $rankine, bool $rounded = true, int $precision = 2): float
    {
        $result = (($rankine - 491.67) / 1.8) + 273.15;

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
     *
     * @since 2.0.0
     *
     * @param   int|float                        $startingLatitude   The latitude of the first point.
     * @param   int|float                        $startingLongitude  The longitude of the first point.
     * @param   int|float                        $endingLatitude     The latitude of the second point.
     * @param   int|float                        $endingLongitude    The longitude of the second point.
     * @param   int                              $precision          Sets the number of decimal digits.
     * @return  array<string, int|float|string>
     */
    public static function haversineDistance(
        int|float $startingLatitude,
        int|float $startingLongitude,
        int|float $endingLatitude,
        int|float $endingLongitude,
        int $precision = 0
    ): array {
        // Radians
        $startingLatitude = deg2rad($startingLatitude);
        $startingLongitude = deg2rad($startingLongitude);
        $endingLatitude = deg2rad($endingLatitude);
        $endingLongitude = deg2rad($endingLongitude);

        // Determine distance
        $latitudinalDistance = $endingLatitude - $startingLatitude;
        $longitudinalDistance = $endingLongitude - $startingLongitude;

        // Square of half the chord length between the two points on the surface of the sphere (Earth)
        $square = sin($latitudinalDistance / 2)
            * sin($latitudinalDistance / 2)
            + cos($startingLatitude)
            * cos($endingLatitude)
            * sin($longitudinalDistance / 2)
            * sin($longitudinalDistance / 2);

        // Central angle
        $centralAngle = 2 * atan2(sqrt($square), sqrt(1 - $square));

        // Earth's radius in meters
        $radius = 6_370_986;

        // great-circle distance between the two points on the surface of the sphere (Earth)
        $distance = $radius * $centralAngle;

        return [
            'meters'     => number_format($distance, $precision),
            'kilometers' => number_format($distance / 1000, $precision),
            'miles'      => number_format($distance / 1609.344, $precision),
        ];
    }
}