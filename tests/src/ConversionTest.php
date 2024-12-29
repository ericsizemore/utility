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

namespace Esi\Utility\Tests;

use Esi\Utility\Conversion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Conversion utility tests.
 *
 * @internal
 *
 * @psalm-api
 */
#[CoversClass(Conversion::class)]
final class ConversionTest extends TestCase
{
    /**
     * Test Conversion::celsiusToFahrenheit().
     */
    #[Test]
    public function celsiusToFahrenheit(): void
    {
        self::assertSame(73.99, Conversion::celsiusToFahrenheit(23.33));
        self::assertSame(74.0, Conversion::celsiusToFahrenheit(23.333_333_333_333_332, false));
    }

    /**
     * Test Conversion::celsiusToKelvin().
     */
    #[Test]
    public function celsiusToKelvin(): void
    {
        self::assertSame(296.48, Conversion::celsiusToKelvin(23.33));
        self::assertSame(296.483_333_333_333_3, Conversion::celsiusToKelvin(23.333_333_333_333_332, false));
    }

    /**
     * Test Conversion::celsiusToRankine().
     */
    #[Test]
    public function celsiusToRankine(): void
    {
        self::assertSame(545.67, Conversion::celsiusToRankine(30));
        self::assertSame(545.670_000_000_000_1, Conversion::celsiusToRankine(30, false));
    }

    /**
     * Test Conversion::fahrenheitToCelsius().
     */
    #[Test]
    public function fahrenheitToCelsius(): void
    {
        self::assertSame(23.33, Conversion::fahrenheitToCelsius(74));
        self::assertSame(23.333_333_333_333_332, Conversion::fahrenheitToCelsius(74, false));
    }

    /**
     * Test Conversion::fahrenheitToKelvin().
     */
    #[Test]
    public function fahrenheitToKelvin(): void
    {
        self::assertSame(296.48, Conversion::fahrenheitToKelvin(74));
        self::assertSame(296.483_333_333_333_3, Conversion::fahrenheitToKelvin(74, false));
    }

    /**
     * Test Conversion::fahrenheitToRankine().
     */
    #[Test]
    public function fahrenheitToRankine(): void
    {
        self::assertSame(533.67, Conversion::fahrenheitToRankine(74));
        self::assertSame(533.670_000_000_000_1, Conversion::fahrenheitToRankine(74, false));
    }

    /**
     * Test Conversion::haversineDistance().
     */
    #[Test]
    public function haversineDistance(): void
    {
        $lat1 = 37.774_9;
        $lon1 = -122.419_4;
        $lat2 = 34.052_2;
        $lon2 = -118.243_7;

        $resultNoPrecision   = Conversion::haversineDistance($lat1, $lon1, $lat2, $lon2);
        $expectedNoPrecision = ['meters' => '559,119', 'kilometers' => '559', 'miles' => '347'];
        self::assertSame($expectedNoPrecision, $resultNoPrecision);

        $resultPrecision   = Conversion::haversineDistance($lat1, $lon1, $lat2, $lon2, 2);
        $expectedPrecision = ['meters' => '559,119.35', 'kilometers' => '559.12', 'miles' => '347.42'];
        self::assertSame($expectedPrecision, $resultPrecision);
    }

    /**
     * Test Conversion::kelvinToCelsius().
     */
    #[Test]
    public function kelvinToCelsius(): void
    {
        self::assertSame(23.33, Conversion::kelvinToCelsius(296.48));
        self::assertSame(23.333_333_333_333_314, Conversion::kelvinToCelsius(296.483_333_333_333_3, false));
    }

    /**
     * Test Conversion::kelvinToFahrenheit().
     */
    #[Test]
    public function kelvinToFahrenheit(): void
    {
        self::assertSame(73.99, Conversion::kelvinToFahrenheit(296.48));
        self::assertSame(73.999_999_999_999_97, Conversion::kelvinToFahrenheit(296.483_333_333_333_3, false));
    }

    /**
     * Test Conversion::kelvinToRankine().
     */
    #[Test]
    public function kelvinToRankine(): void
    {
        self::assertSame(234.0, Conversion::kelvinToRankine(130));
        self::assertSame(234.000_000_000_000_06, Conversion::kelvinToRankine(130, false));
    }

    /**
     * Test Conversion::rankineToCelsius().
     */
    #[Test]
    public function rankineToCelsius(): void
    {
        self::assertSame(30.0, Conversion::rankineToCelsius(545.67));
        self::assertSame(29.999_999_999_999_968, Conversion::rankineToCelsius(545.67, false));
    }

    /**
     * Test Conversion::rankineToFahrenheit().
     */
    #[Test]
    public function rankineToFahrenheit(): void
    {
        self::assertSame(74.0, Conversion::rankineToFahrenheit(533.67));
        self::assertSame(74.000_000_000_000_06, Conversion::rankineToFahrenheit(533.670_000_000_000_1, false));
    }

    /**
     * Test Conversion::rankineToKelvin().
     */
    #[Test]
    public function rankineToKelvin(): void
    {
        self::assertSame(130.0, Conversion::rankineToKelvin(234.0));
        self::assertSame(129.999_999_999_999_97, Conversion::rankineToKelvin(234.0, false));
    }
}
