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

use Esi\Utility\Numbers;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use ValueError;

use function intval;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * Number utility tests.
 */
#[CoversClass(Numbers::class)]
class NumbersTest extends TestCase
{
    /**
     * Test Numbers::inside() with int's.
     */
    public function testInsideInt(): void
    {
        self::assertTrue(Numbers::inside(25, 24, 26));
        self::assertFalse(Numbers::inside(25, 26, 27));
    }

    /**
     * Test Numbers::inside() with float's.
     */
    public function testInsideFloat(): void
    {
        self::assertTrue(Numbers::inside(25.0, 24.0, 26.0));
        self::assertFalse(Numbers::inside(25.0, 26.0, 27.0));
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
     * Test Numbers::outside() with float's.
     */
    public function testOutsideFloat(): void
    {
        self::assertTrue(Numbers::outside(23.0, 24.0, 26.0));
        self::assertFalse(Numbers::outside(25.0, 24.0, 26.0));
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
        $int = Numbers::random((int) (PHP_INT_MIN - 1), PHP_INT_MAX);
        $int = Numbers::random(PHP_INT_MAX, PHP_INT_MIN);
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
        self::assertSame('1001st', Numbers::ordinal(1001));
        self::assertSame('1002nd', Numbers::ordinal(1002));
        self::assertSame('1003rd', Numbers::ordinal(1003));
        self::assertSame('1004th', Numbers::ordinal(1004));
        self::assertSame('10001st', Numbers::ordinal(10001));
        self::assertSame('10002nd', Numbers::ordinal(10002));
        self::assertSame('10003rd', Numbers::ordinal(10003));
        self::assertSame('10004th', Numbers::ordinal(10004));
    }

    /**
     * Test Numbers::sizeFormat() with binary standard.
     */
    public function testSizeFormatBinary(): void
    {
        // Test 'binary' standard formatting.
        $size = Numbers::sizeFormat(512);
        self::assertSame('512 B', $size);

        $size = Numbers::sizeFormat(2048, 1);
        self::assertSame('2.0 KiB', $size);

        $size = Numbers::sizeFormat(25_151_251, 2);
        self::assertSame('23.99 MiB', $size);

        $size = Numbers::sizeFormat(19_971_597_926, 2);
        self::assertSame('18.60 GiB', $size);

        $size = Numbers::sizeFormat(2_748_779_069_440, 1);
        self::assertSame('2.5 TiB', $size);

        $size = Numbers::sizeFormat(2_748_779_069_440 * 1024, 1);
        self::assertSame('2.5 PiB', $size);

        $size = Numbers::sizeFormat(2_748_779_069_440 * (1024 * 1024), 1);
        self::assertSame('2.5 EiB', $size);
    }

    /**
     * Test Numbers::sizeFormat() with metric standard.
     */
    public function testSizeFormatMetric(): void
    {
        // Test 'metric' standard formatting.
        $size = Numbers::sizeFormat(512, standard: 'metric');
        self::assertSame('512 B', $size);

        $size = Numbers::sizeFormat(2000, 1, 'metric');
        self::assertSame('2.0 kB', $size);

        $size = Numbers::sizeFormat(25_151_251, 2, 'metric');
        self::assertSame('25.15 MB', $size);

        $size = Numbers::sizeFormat(19_971_597_926, 2, 'metric');
        self::assertSame('19.97 GB', $size);

        $size = Numbers::sizeFormat(2_748_779_069_440, 1, 'metric');
        self::assertSame('2.7 TB', $size);

        $size = Numbers::sizeFormat(2_748_779_069_440 * 1000, 1, 'metric');
        self::assertSame('2.7 PB', $size);

        $size = Numbers::sizeFormat(2_748_779_069_440 * (1000 * 1000), 1, 'metric');
        self::assertSame('2.7 EB', $size);
    }

    /**
     * Test Numbers::sizeFormat() with an invalid standard.
     */
    public function testSizeFormatInvalidStandard(): void
    {
        // Test if we provide an invalid $standard option (should throw an exception).
        self::expectException(\InvalidArgumentException::class);
        $size = Numbers::sizeFormat(2048, 1, 'notanoption');
    }
}
