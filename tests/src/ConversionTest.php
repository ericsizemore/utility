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

namespace Esi\Utility\Tests;

use Esi\Utility\Conversion;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Conversion utility tests.
 */
#[CoversClass(Conversion::class)]
class ConversionTest extends TestCase
{
    /**
     * Test Conversion::fahrenheitToCelsius().
     */
    public function testFahrenheitToCelsius(): void
    {
        self::assertEquals(23.33, Conversion::fahrenheitToCelsius(74));
        self::assertEquals(23.333333333333332, Conversion::fahrenheitToCelsius(74, false));
    }

    /**
     * Test Conversion::celsiusToFahrenheit().
     */
    public function testCelsiusToFahrenheit(): void
    {
        self::assertEquals(73.99, Conversion::celsiusToFahrenheit(23.33));
        self::assertEquals(74, Conversion::celsiusToFahrenheit(23.333333333333332, false));
    }

    /**
     * Test Conversion::celsiusToKelvin().
     */
    public function testCelsiusToKelvin(): void
    {
        self::assertEquals(296.48, Conversion::celsiusToKelvin(23.33));
        self::assertEquals(296.4833333333333, Conversion::celsiusToKelvin(23.333333333333332, false));
    }

    /**
     * Test Conversion::kelvinToCelsius().
     */
    public function testKelvinToCelsius(): void
    {
        self::assertEquals(23.33, Conversion::kelvinToCelsius(296.48));
        self::assertEquals(23.333333333333314, Conversion::kelvinToCelsius(296.4833333333333, false));
    }

    /**
     * Test Conversion::fahrenheitToKelvin().
     */
    public function testFahrenheitToKelvin(): void
    {
        self::assertEquals(296.48, Conversion::fahrenheitToKelvin(74));
        self::assertEquals(296.4833333333333, Conversion::fahrenheitToKelvin(74, false));
    }

    /**
     * Test Conversion::kelvinToFahrenheit().
     */
    public function testKelvinToFahrenheit(): void
    {
        self::assertEquals(73.99, Conversion::kelvinToFahrenheit(296.48));
        self::assertEquals(73.99999999999997, Conversion::kelvinToFahrenheit(296.4833333333333, false));
    }

    /**
     * Test Conversion::fahrenheitToRankine().
     */
    public function testFahrenheitToRankine(): void
    {
        self::assertEquals(533.67, Conversion::fahrenheitToRankine(74));
        self::assertEquals(533.6700000000001, Conversion::fahrenheitToRankine(74, false));
    }

    /**
     * Test Conversion::rankineToFahrenheit().
     */
    public function testRankineToFahrenheit(): void
    {
        self::assertEquals(74, Conversion::rankineToFahrenheit(533.67));
        self::assertEquals(74.00000000000006, Conversion::rankineToFahrenheit(533.6700000000001, false));
    }

    /**
     * Test Conversion::celsiusToRankine().
     */
    public function testCelsiusToRankine(): void
    {
        self::assertEquals(545.67, Conversion::celsiusToRankine(30));
        self::assertEquals(545.6700000000001, Conversion::celsiusToRankine(30, false));
    }

    /**
     * Test Conversion::rankineToCelsius().
     */
    public function testRankineToCelsius(): void
    {
        self::assertEquals(30, Conversion::rankineToCelsius(545.67));
        self::assertEquals(29.999999999999968, Conversion::rankineToCelsius(545.67, false));
    }

    /**
     * Test Conversion::kelvinToRankine().
     */
    public function testKelvinToRankine(): void
    {
        self::assertEquals(234.0, Conversion::kelvinToRankine(130));
        self::assertEquals(234.00000000000006, Conversion::kelvinToRankine(130, false));
    }

    /**
     * Test Conversion::rankineToKelvin().
     */
    public function testRankineToKelvin(): void
    {
        self::assertEquals(130, Conversion::rankineToKelvin(234.0));
        self::assertEquals(129.99999999999997, Conversion::rankineToKelvin(234.0, false));
    }

    /**
     * Test Conversion::haversineDistance().
     */
    public function testHaversineDistance(): void
    {
        $lat1 = 37.7749;
        $lon1 = -122.4194;
        $lat2 = 34.0522;
        $lon2 = -118.2437;

        $resultNoPrecision = Conversion::haversineDistance($lat1, $lon1, $lat2, $lon2);
        $expectedNoPrecision = ['meters' => '559,119', 'kilometers' => '559', 'miles' => '347'];
        self::assertSame($expectedNoPrecision, $resultNoPrecision);

        $resultPrecision = Conversion::haversineDistance($lat1, $lon1, $lat2, $lon2, 2);
        $expectedPrecision = ['meters' => '559,119.35', 'kilometers' => '559.12', 'miles' => '347.42'];
        self::assertSame($expectedPrecision, $resultPrecision);
    }
}
