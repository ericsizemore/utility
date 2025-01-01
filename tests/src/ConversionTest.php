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
use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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
     * Tests Celsius conversion methods.
     */
    #[Test]
    #[DataProvider('provideCelsiusConversionData')]
    public function celsiusConversions(
        float $input,
        float $fahrenheit,
        float $kelvin,
        float $rankine,
        bool $rounded
    ): void {
        if ($rounded) {
            self::assertSame($fahrenheit, Conversion::celsiusToFahrenheit($input));
            self::assertSame($kelvin, Conversion::celsiusToKelvin($input));
            self::assertSame($rankine, Conversion::celsiusToRankine($input));
        } else {
            self::assertEqualsWithDelta($fahrenheit, Conversion::celsiusToFahrenheit($input, false), 0.0_000_000_001);
            self::assertEqualsWithDelta($kelvin, Conversion::celsiusToKelvin($input, false), 0.0_000_000_001);
            self::assertEqualsWithDelta($rankine, Conversion::celsiusToRankine($input, false), 0.0_000_000_001);
        }
    }

    /**
     * Tests Fahrenheit conversion methods.
     */
    #[Test]
    #[DataProvider('provideFahrenheitConversionData')]
    public function fahrenheitConversions(
        float $input,
        float $celsius,
        float $kelvin,
        float $rankine,
        bool $rounded
    ): void {
        self::assertSame($celsius, Conversion::fahrenheitToCelsius($input, $rounded));
        self::assertSame($kelvin, Conversion::fahrenheitToKelvin($input, $rounded));
        self::assertSame($rankine, Conversion::fahrenheitToRankine($input, $rounded));
    }

    /**
     * Tests the Haversine distance calculation.
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
     * Tests edge cases for Haversine distance calculation.
     */
    #[Test]
    public function haversineDistanceEdgeCases(): void
    {
        // Test same point (zero distance)
        $zeroDistance = Conversion::haversineDistance(0.0, 0.0, 0.0, 0.0);
        self::assertSame('0', $zeroDistance['meters']);

        // Test antipodal points (maximum distance)
        $maxDistance = Conversion::haversineDistance(90.0, 0.0, -90.0, 0.0);
        self::assertGreaterThan('20,000,000', $maxDistance['meters']);

        // Test equator crossing
        $equatorCross = Conversion::haversineDistance(1.0, 0.0, -1.0, 0.0);
        self::assertArrayHasKey('kilometers', $equatorCross);
        self::assertArrayHasKey('miles', $equatorCross);
    }

    /**
     * Tests Kelvin conversion methods.
     */
    #[Test]
    #[DataProvider('provideKelvinConversionData')]
    public function kelvinConversions(
        float $input,
        float $celsius,
        float $fahrenheit,
        float $rankine,
        bool $rounded
    ): void {
        self::assertSame($celsius, Conversion::kelvinToCelsius($input, $rounded));
        self::assertSame($fahrenheit, Conversion::kelvinToFahrenheit($input, $rounded));
        self::assertSame($rankine, Conversion::kelvinToRankine($input, $rounded));
    }

    /**
     * Tests Rankine conversion methods.
     */
    #[Test]
    #[DataProvider('provideRankineConversionData')]
    public function rankineConversions(
        float $input,
        float $celsius,
        float $fahrenheit,
        float $kelvin,
        bool $rounded
    ): void {
        if ($rounded) {
            self::assertSame($celsius, Conversion::rankineToCelsius($input));
            self::assertSame($fahrenheit, Conversion::rankineToFahrenheit($input));
            self::assertSame($kelvin, Conversion::rankineToKelvin($input));
        } else {
            self::assertEqualsWithDelta($celsius, Conversion::rankineToCelsius($input, false), 0.0_000_000_001);
            self::assertEqualsWithDelta($fahrenheit, Conversion::rankineToFahrenheit($input, false), 0.0_000_000_001);
            self::assertEqualsWithDelta($kelvin, Conversion::rankineToKelvin($input, false), 0.0_000_000_001);
        }
    }

    /**
     * Tests temperature conversion consistency (round-trip conversions).
     */
    #[Test]
    public function temperatureConversionConsistency(): void
    {
        $celsius = 25.0;

        // Test Celsius -> Other -> Celsius
        $fahrenheit = Conversion::celsiusToFahrenheit($celsius, false);
        $kelvin     = Conversion::celsiusToKelvin($celsius, false);
        $rankine    = Conversion::celsiusToRankine($celsius, false);

        self::assertEqualsWithDelta(
            $celsius,
            Conversion::fahrenheitToCelsius($fahrenheit, false),
            0.0000001,
            'Celsius -> Fahrenheit -> Celsius conversion failed'
        );

        self::assertEqualsWithDelta(
            $celsius,
            Conversion::kelvinToCelsius($kelvin, false),
            0.0000001,
            'Celsius -> Kelvin -> Celsius conversion failed'
        );

        self::assertEqualsWithDelta(
            $celsius,
            Conversion::rankineToCelsius($rankine, false),
            0.0000001,
            'Celsius -> Rankine -> Celsius conversion failed'
        );
    }

    /**
     * Provides test data for Celsius conversion tests.
     *
     * @return Iterator<string, array{input: float, fahrenheit: float, kelvin: float, rankine: float, rounded: bool}>
     */
    public static function provideCelsiusConversionData(): Iterator
    {
        yield 'standard values' => [
            'input'      => 23.33,
            'fahrenheit' => 73.99,
            'kelvin'     => 296.48,
            'rankine'    => 533.66,
            'rounded'    => true,
        ];
        yield 'high precision' => [
            'input'      => 23.333_333_333_333_332,
            'fahrenheit' => 74.0,
            'kelvin'     => 296.483_333_333_333_3,
            'rankine'    => 533.67,
            'rounded'    => false,
        ];
    }

    /**
     * Provides test data for Fahrenheit conversion tests.
     *
     * @return Iterator<string, array{input: float, celsius: float, kelvin: float, rankine: float, rounded: bool}>
     */
    public static function provideFahrenheitConversionData(): Iterator
    {
        yield 'standard values' => [
            'input'   => 74.0,
            'celsius' => 23.33,
            'kelvin'  => 296.48,
            'rankine' => 533.67,
            'rounded' => true,
        ];
        yield 'high precision' => [
            'input'   => 74.0,
            'celsius' => 23.333_333_333_333_332,
            'kelvin'  => 296.483_333_333_333_3,
            'rankine' => 533.670_000_000_000_1,
            'rounded' => false,
        ];
    }

    /**
     * Provides test data for Kelvin conversion tests.
     *
     * @return Iterator<string, array{input: float, celsius: float, fahrenheit: float, rankine: float, rounded: bool}>
     */
    public static function provideKelvinConversionData(): Iterator
    {
        yield 'standard values' => [
            'input'      => 296.48,
            'celsius'    => 23.33,
            'fahrenheit' => 73.99,
            'rankine'    => 533.66,
            'rounded'    => true,
        ];
        yield 'high precision' => [
            'input'      => 296.483_333_333_333_3,
            'celsius'    => 23.333_333_333_333_314,
            'fahrenheit' => 73.999_999_999_999_97,
            'rankine'    => 533.67,
            'rounded'    => false,
        ];
    }

    /**
     * Provides test data for Rankine conversion tests.
     *
     * @return Iterator<string, array{input: float, celsius: float, fahrenheit: float, kelvin: float, rounded: bool}>
     */
    public static function provideRankineConversionData(): Iterator
    {
        yield 'standard values' => [
            'input'      => 533.67,
            'celsius'    => 23.33,
            'fahrenheit' => 74.0,
            'kelvin'     => 296.48,
            'rounded'    => true,
        ];
        yield 'high precision' => [
            'input'      => 533.670_000_000_000_1,
            'celsius'    => 23.333_333_333_333_364,
            'fahrenheit' => 74.000_000_000_000_06,
            'kelvin'     => 296.483_333_333_333_35,
            'rounded'    => false,
        ];
    }
}
