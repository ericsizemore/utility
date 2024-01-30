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

use Esi\Utility\Dates;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use InvalidArgumentException;
use RuntimeException;

use function time;

/**
 * Date utility tests.
 */
#[CoversClass(Dates::class)]
class DatesTest extends TestCase
{
    /**
     * Test Dates::timeDifference().
     */
    public function testTimeDifference(): void
    {
        self::assertSame('1 second(s) old', Dates::timeDifference(time() - 1));
        self::assertSame('30 second(s) old', Dates::timeDifference(time() - 30));
        self::assertSame('1 minute(s) old', Dates::timeDifference(time() - 60));
        self::assertSame('5 minute(s) old', Dates::timeDifference(time() - (60 * 5)));
        self::assertSame('1 hour(s) old', Dates::timeDifference(time() - (3_600)));
        self::assertSame('2 hour(s) old', Dates::timeDifference(time() - (3_600 * 2)));
        self::assertSame('1 day(s) old', Dates::timeDifference(time() - (3_600 * 24)));
        self::assertSame('5 day(s) old', Dates::timeDifference(time() - (3_600 * 24 * 5)));
        self::assertSame('1 week(s) old', Dates::timeDifference(time() - (3_600 * 24 * 7)));
        self::assertSame('2 week(s) old', Dates::timeDifference(time() - (3_600 * 24 * 14)));
        self::assertSame('1 month(s) old', Dates::timeDifference(time() - (604_800 * 5)));
        self::assertSame('2 month(s) old', Dates::timeDifference(time() - (604_800 * 10)));
        self::assertSame('1 year(s) old', Dates::timeDifference(time() - (2_592_000 * 15)));
        self::assertSame('2 year(s) old', Dates::timeDifference(time() - (2_592_000 * 36)));
        self::assertSame('11 year(s) old', Dates::timeDifference(time() - (2_592_000 * 140)));
        self::assertSame('1 second(s) old', Dates::timeDifference(time() - 1, 0, ''));
    }

    /**
     * Test Dates::timeDifference() with invalid $timestampFrom and $timestampTo.
     */
    public function testInvalidTimeDifference(): void
    {
        self::expectException(InvalidArgumentException::class);
        Dates::timeDifference(0);
        Dates::timeDifference(0, 0);
        Dates::timeDifference(0, 123_456_789_012);
        Dates::timeDifference(123_456_789_012);
    }

    /**
     * Test Dates::timeDifference() with invalid timezone.
     */
    public function testInvalidTimezoneTimeDifference(): void
    {
        self::expectException(RuntimeException::class);
        Dates::timeDifference(time() - 30, timezone: 'INVALID');
    }

    /**
     * Test Dates::timezoneInfo()
     */
    public function testTimezoneInfo(): void
    {
        $zoneInfo = Dates::timezoneInfo('America/New_York');
        $expected = ($zoneInfo['dst'] === 1) ? -4 : -5;

        self::assertSame($expected, $zoneInfo['offset']);
        self::assertSame('US', $zoneInfo['country']);

        $zoneInfo = Dates::timezoneInfo('');
        $expected = 0;

        self::assertSame($expected, $zoneInfo['offset']);
        self::assertSame('??', $zoneInfo['country']);

        self::expectException(RuntimeException::class);
        Dates::timezoneInfo('INVALID');
    }

    /**
     * Test Dates::validTimezone().
     */
    public function testValidTimezone(): void
    {
        self::assertFalse(Dates::validTimezone('InvalidTimezone'));
        self::assertTrue(Dates::validTimezone('America/New_York'));
    }
}
