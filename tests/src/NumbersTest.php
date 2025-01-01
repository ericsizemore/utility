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

use Esi\Utility\Numbers;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * Number utility tests.
 *
 * @internal
 *
 * @psalm-api
 */
#[CoversClass(Numbers::class)]
final class NumbersTest extends TestCase
{
    /**
     * Test Numbers::inside() with float's.
     */
    #[Test]
    public function insideCanWorkWithFloats(): void
    {
        self::assertTrue(Numbers::inside(25.0, 24.0, 26.0));
        self::assertFalse(Numbers::inside(25.0, 26.0, 27.0));
    }

    /**
     * Test Numbers::inside() with int's.
     */
    #[Test]
    public function insideCanWorkWithIntegers(): void
    {
        self::assertTrue(Numbers::inside(25, 24, 26));
        self::assertFalse(Numbers::inside(25, 26, 27));
    }

    /**
     * Test Numbers::ordinal().
     */
    #[DataProvider('ordinalDataProvider')]
    #[Test]
    public function ordinalReturnsProperValues(int $number, string $expected): void
    {
        self::assertSame($expected, Numbers::ordinal($number));
    }

    /**
     * Test Numbers::outside() with float's.
     */
    #[Test]
    public function outsideCanWorkWithFloats(): void
    {
        self::assertTrue(Numbers::outside(23.0, 24.0, 26.0));
        self::assertFalse(Numbers::outside(25.0, 24.0, 26.0));
    }

    /**
     * Test Numbers::outside() with int's.
     */
    #[Test]
    public function outsideCanWorkWithIntegers(): void
    {
        self::assertTrue(Numbers::outside(23, 24, 26));
        self::assertFalse(Numbers::outside(25, 24, 26));
    }

    /**
     * Test Numbers::randomInt().
     */
    #[Test]
    public function randomIntCanReturnProperValueOrException(): void
    {
        $int = Numbers::random(100, 250);
        self::assertGreaterThanOrEqual(100, $int);
        self::assertLessThanOrEqual(250, $int);

        self::expectException(ValueError::class);
        Numbers::random((int) (PHP_INT_MIN - 1), PHP_INT_MAX);
        Numbers::random(PHP_INT_MAX, PHP_INT_MIN);
    }

    /**
     * Test Numbers::sizeFormat() with an invalid standard.
     */
    #[Test]
    public function sizeFormatThrowsExceptionForInvalidStandard(): void
    {
        // Test if we provide an invalid $standard option (should throw an exception).
        self::expectException(InvalidArgumentException::class);
        Numbers::sizeFormat(2_048, 1, 'notanoption');
    }

    /**
     * Test Numbers::sizeFormat() with binary standard.
     */
    #[DataProvider('sizeFormatWithBinaryProvider')]
    #[Test]
    public function sizeFormatWithBinary(int $bytes, int $precision, string $expected): void
    {
        $size = Numbers::sizeFormat($bytes, $precision);
        self::assertSame($expected, $size);
    }

    /**
     * Test Numbers::sizeFormat() with metric standard.
     */
    #[DataProvider('sizeFormatWithMetricProvider')]
    #[Test]
    public function sizeFormatWithMetric(int $bytes, int $precision, string $expected): void
    {
        $size = Numbers::sizeFormat($bytes, $precision, 'metric');
        self::assertSame($expected, $size);
    }

    /**
     * @return Generator<int, array{0: int, 1: string}, mixed, void>
     */
    public static function ordinalDataProvider(): Generator
    {
        yield [1, '1st'];
        yield [2, '2nd'];
        yield [3, '3rd'];
        yield [4, '4th'];
        yield [5, '5th'];
        yield [6, '6th'];
        yield [7, '7th'];
        yield [8, '8th'];
        yield [9, '9th'];
        yield [11, '11th'];
        yield [15, '15th'];
        yield [22, '22nd'];
        yield [23, '23rd'];
        yield [102, '102nd'];
        yield [104, '104th'];
        yield [143, '143rd'];
        yield [1_001, '1001st'];
        yield [1_002, '1002nd'];
        yield [1_003, '1003rd'];
        yield [1_004, '1004th'];
        yield [10_001, '10001st'];
        yield [10_002, '10002nd'];
        yield [10_003, '10003rd'];
        yield [10_004, '10004th'];
    }

    /**
     * Data provider for binary size formatting tests.
     *
     * @return Generator<int, array{
     *     bytes: int,
     *     precision: int,
     *     expected: string
     * }, mixed, void>
     */
    public static function sizeFormatWithBinaryProvider(): Generator
    {
        yield [
            'bytes'     => 512,
            'precision' => 0,
            'expected'  => '512 B',
        ];

        yield [
            'bytes'     => 2_048,
            'precision' => 1,
            'expected'  => '2.0 KiB',
        ];

        yield [
            'bytes'     => 25_151_251,
            'precision' => 2,
            'expected'  => '23.99 MiB',
        ];

        yield [
            'bytes'     => 19_971_597_926,
            'precision' => 2,
            'expected'  => '18.60 GiB',
        ];

        yield [
            'bytes'     => 2_748_779_069_440,
            'precision' => 1,
            'expected'  => '2.5 TiB',
        ];

        yield [
            'bytes'     => 2_748_779_069_440 * 1_024,
            'precision' => 1,
            'expected'  => '2.5 PiB',
        ];

        yield [
            'bytes'     => 2_748_779_069_440 * (1_024 * 1_024),
            'precision' => 1,
            'expected'  => '2.5 EiB',
        ];
    }

    /**
     * Data provider for metric size formatting tests.
     *
     * @return Generator<int, array{
     *     bytes: int,
     *     precision: int,
     *     expected: string
     * }, mixed, void>
     */
    public static function sizeFormatWithMetricProvider(): Generator
    {
        yield [
            'bytes'     => 512,
            'precision' => 0,
            'expected'  => '512 B',
        ];

        yield [
            'bytes'     => 2_000,
            'precision' => 1,
            'expected'  => '2.0 kB',
        ];

        yield [
            'bytes'     => 25_151_251,
            'precision' => 2,
            'expected'  => '25.15 MB',
        ];

        yield [
            'bytes'     => 19_971_597_926,
            'precision' => 2,
            'expected'  => '19.97 GB',
        ];

        yield [
            'bytes'     => 2_748_779_069_440,
            'precision' => 1,
            'expected'  => '2.7 TB',
        ];

        yield [
            'bytes'     => 2_748_779_069_440 * 1_000,
            'precision' => 1,
            'expected'  => '2.7 PB',
        ];

        yield [
            'bytes'     => 2_748_779_069_440 * (1_000 * 1_000),
            'precision' => 1,
            'expected'  => '2.7 EB',
        ];
    }
}
