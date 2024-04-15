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

use Esi\Utility\Arrays;
use Esi\Utility\Dates;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function time;

/**
 * Date utility tests.
 *
 * @internal
 */
#[CoversClass(Dates::class)]
#[CoversMethod(Arrays::class, 'valueExists')]
class DatesTest extends TestCase
{
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
     * Test Dates::timezoneInfo().
     */
    public function testTimezoneInfo(): void
    {
        $zoneInfo = Dates::timezoneInfo('America/New_York');
        $expected = ((bool) $zoneInfo['dst']) ? -4 : -5;

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
