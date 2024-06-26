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

namespace Esi\Utility\Tests;

use Esi\Utility\Numbers;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ValueError;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * Number utility tests.
 *
 * @internal
 */
#[CoversClass(Numbers::class)]
class NumbersTest extends TestCase
{
    /**
     * Test Numbers::inside() with float's.
     */
    public function testInsideFloat(): void
    {
        self::assertTrue(Numbers::inside(25.0, 24.0, 26.0));
        self::assertFalse(Numbers::inside(25.0, 26.0, 27.0));
    }

    /**
     * Test Numbers::inside() with int's.
     */
    public function testInsideInt(): void
    {
        self::assertTrue(Numbers::inside(25, 24, 26));
        self::assertFalse(Numbers::inside(25, 26, 27));
    }

    /**
     * Test Numbers::ordinal().
     */
    public function testOrdinal(): void
    {
        self::assertSame('1st', Numbers::ordinal(1));
        self::assertSame('2nd', Numbers::ordinal(2));
        self::assertSame('3rd', Numbers::ordinal(3));
        self::assertSame('4th', Numbers::ordinal(4));
        self::assertSame('5th', Numbers::ordinal(5));
        self::assertSame('6th', Numbers::ordinal(6));
        self::assertSame('7th', Numbers::ordinal(7));
        self::assertSame('8th', Numbers::ordinal(8));
        self::assertSame('9th', Numbers::ordinal(9));
        self::assertSame('11th', Numbers::ordinal(11));
        self::assertSame('15th', Numbers::ordinal(15));
        self::assertSame('22nd', Numbers::ordinal(22));
        self::assertSame('23rd', Numbers::ordinal(23));
        self::assertSame('102nd', Numbers::ordinal(102));
        self::assertSame('104th', Numbers::ordinal(104));
        self::assertSame('143rd', Numbers::ordinal(143));
        self::assertSame('1001st', Numbers::ordinal(1_001));
        self::assertSame('1002nd', Numbers::ordinal(1_002));
        self::assertSame('1003rd', Numbers::ordinal(1_003));
        self::assertSame('1004th', Numbers::ordinal(1_004));
        self::assertSame('10001st', Numbers::ordinal(10_001));
        self::assertSame('10002nd', Numbers::ordinal(10_002));
        self::assertSame('10003rd', Numbers::ordinal(10_003));
        self::assertSame('10004th', Numbers::ordinal(10_004));
    }

    /**
     * Test Numbers::outside() with float's.
     */
    public function testOutsideFloat(): void
    {
        self::assertTrue(Numbers::outside(23.0, 24.0, 26.0));
        self::assertFalse(Numbers::outside(25.0, 24.0, 26.0));
    }

    /**
     * Test Numbers::outside() with int's.
     */
    public function testOutsideInt(): void
    {
        self::assertTrue(Numbers::outside(23, 24, 26));
        self::assertFalse(Numbers::outside(25, 24, 26));
    }

    /**
     * Test Numbers::randomInt().
     */
    public function testRandomInt(): void
    {
        $int = Numbers::random(100, 250);
        self::assertGreaterThanOrEqual(100, $int);
        self::assertLessThanOrEqual(250, $int);

        self::expectException(ValueError::class);
        Numbers::random((int) (PHP_INT_MIN - 1), PHP_INT_MAX);
        Numbers::random(PHP_INT_MAX, PHP_INT_MIN);
    }

    /**
     * Test Numbers::sizeFormat() with binary standard.
     */
    public function testSizeFormatBinary(): void
    {
        // Test 'binary' standard formatting.
        $size = Numbers::sizeFormat(512);
        self::assertSame('512 B', $size);

        $size = Numbers::sizeFormat(2_048, 1);
        self::assertSame('2.0 KiB', $size);

        $size = Numbers::sizeFormat(25_151_251, 2);
        self::assertSame('23.99 MiB', $size);

        $size = Numbers::sizeFormat(19_971_597_926, 2);
        self::assertSame('18.60 GiB', $size);

        $size = Numbers::sizeFormat(2_748_779_069_440, 1);
        self::assertSame('2.5 TiB', $size);

        $size = Numbers::sizeFormat(2_748_779_069_440 * 1_024, 1);
        self::assertSame('2.5 PiB', $size);

        $size = Numbers::sizeFormat(2_748_779_069_440 * (1_024 * 1_024), 1);
        self::assertSame('2.5 EiB', $size);
    }

    /**
     * Test Numbers::sizeFormat() with an invalid standard.
     */
    public function testSizeFormatInvalidStandard(): void
    {
        // Test if we provide an invalid $standard option (should throw an exception).
        self::expectException(InvalidArgumentException::class);
        Numbers::sizeFormat(2_048, 1, 'notanoption');
    }

    /**
     * Test Numbers::sizeFormat() with metric standard.
     */
    public function testSizeFormatMetric(): void
    {
        // Test 'metric' standard formatting.
        $size = Numbers::sizeFormat(512, standard: 'metric');
        self::assertSame('512 B', $size);

        $size = Numbers::sizeFormat(2_000, 1, 'metric');
        self::assertSame('2.0 kB', $size);

        $size = Numbers::sizeFormat(25_151_251, 2, 'metric');
        self::assertSame('25.15 MB', $size);

        $size = Numbers::sizeFormat(19_971_597_926, 2, 'metric');
        self::assertSame('19.97 GB', $size);

        $size = Numbers::sizeFormat(2_748_779_069_440, 1, 'metric');
        self::assertSame('2.7 TB', $size);

        $size = Numbers::sizeFormat(2_748_779_069_440 * 1_000, 1, 'metric');
        self::assertSame('2.7 PB', $size);

        $size = Numbers::sizeFormat(2_748_779_069_440 * (1_000 * 1_000), 1, 'metric');
        self::assertSame('2.7 EB', $size);
    }
}
