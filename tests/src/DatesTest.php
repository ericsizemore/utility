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

use Esi\Clock\FrozenClock;
use Esi\Utility\Arrays;
use Esi\Utility\Dates;
use InvalidArgumentException;
use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

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
    #[DataProvider('invalidTimestampProvider')]
    public function testInvalidTimeDifference(int $timestampFrom, int $timestampTo): void
    {
        self::expectException(InvalidArgumentException::class);
        Dates::timeDifference($timestampFrom, $timestampTo);
    }

    /**
     * Test Dates::timeDifference() with invalid timezone.
     */
    public function testInvalidTimezoneTimeDifference(): void
    {
        self::expectException(RuntimeException::class);
        Dates::timeDifference(FrozenClock::fromUtc()->now()->getTimestamp() - 30, timezone: 'INVALID');
    }

    /**
     * Test Dates::timeDifference().
     *
     * @param array{0: int, 1?: int, 2?: string} $args
     */
    #[DataProvider('timeDifferenceProvider')]
    public function testTimeDifference(string $expected, array $args): void
    {
        self::assertSame($expected, Dates::timeDifference(...$args));
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

    public static function invalidTimestampProvider(): Iterator
    {
        yield [0, 0];
        yield [0, 1_234_567];
        yield [1_234_567, 0];
        yield [0, PHP_INT_MAX];
        yield [PHP_INT_MAX, 0];
    }

    public static function timeDifferenceProvider(): Iterator
    {
        static $frozenTimestamp;

        $frozenTimestamp ??= FrozenClock::fromUtc()->now()->getTimestamp();

        yield ['1 second(s) old', [($frozenTimestamp - 1)]];
        yield ['30 second(s) old', [($frozenTimestamp - 30)]];
        yield ['1 minute(s) old', [($frozenTimestamp - 60)]];
        yield ['5 minute(s) old', [($frozenTimestamp - (60 * 5))]];
        yield ['1 hour(s) old', [($frozenTimestamp - (3_600))]];
        yield ['2 hour(s) old', [($frozenTimestamp - (3_600 * 2))]];
        yield ['1 day(s) old', [($frozenTimestamp - (3_600 * 24))]];
        yield ['5 day(s) old', [($frozenTimestamp - (3_600 * 24 * 5))]];
        yield ['1 week(s) old', [($frozenTimestamp - (3_600 * 24 * 7))]];
        yield ['2 week(s) old', [($frozenTimestamp - (3_600 * 24 * 14))]];
        yield ['1 month(s) old', [($frozenTimestamp - (604_800 * 5))]];
        yield ['2 month(s) old', [($frozenTimestamp - (604_800 * 10))]];
        yield ['1 year(s) old', [($frozenTimestamp - (2_592_000 * 15))]];
        yield ['2 year(s) old', [($frozenTimestamp - (2_592_000 * 36))]];
        yield ['11 year(s) old', [($frozenTimestamp - (2_592_000 * 140))]];
        yield ['1 second(s) old', [($frozenTimestamp - 1), 0, '']];
    }
}
