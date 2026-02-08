<?php

declare(strict_types=1);

/**
 * This file is part of Esi\Utility.
 *
 * (c) 2017 - 2026 Eric Sizemore <admin@secondversion.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 */

namespace Esi\Utility\Tests;

use Esi\Clock\FrozenClock;
use Esi\Utility\Arrays;
use Esi\Utility\Dates;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function date_default_timezone_set;

/**
 * Date utility tests.
 *
 * @internal
 *
 * @psalm-api
 */
#[CoversClass(Dates::class)]
#[CoversMethod(Arrays::class, 'valueExists')]
final class DatesTest extends TestCase
{
    private const FrozenTimestamp = 1736132798;

    protected function setUp(): void
    {
        parent::setUp();

        date_default_timezone_set(Dates::DEFAULT_TIMEZONE);
    }

    /**
     * Test extended time difference formatting.
     */
    #[DataProvider('extendedTimeDifferenceProvider')]
    #[Test]
    public function extendedTimeDifference(int $seconds, string $expected): void
    {
        $timestampTo   = FrozenClock::fromUtc()->now()->setTimestamp(self::FrozenTimestamp);
        $timestampFrom = $timestampTo->getTimestamp() - $seconds;

        self::assertSame($expected, Dates::timeDifference($timestampFrom, $timestampTo->getTimestamp(), extendedOutput: true));
    }

    /**
     * Test Dates::timeDifference() with invalid $timestampFrom and $timestampTo.
     */
    #[DataProvider('invalidTimestampProvider')]
    #[Test]
    public function invalidTimeDifference(int $timestampFrom, int $timestampTo): void
    {
        self::expectException(InvalidArgumentException::class);
        Dates::timeDifference($timestampFrom, $timestampTo);
    }

    /**
     * Test Dates::timeDifference() with invalid timezone.
     */
    #[Test]
    public function invalidTimezoneTimeDifference(): void
    {
        $clock = FrozenClock::fromUtc()->now();

        self::expectException(RuntimeException::class);
        Dates::timeDifference($clock->modify('-30 seconds')->getTimestamp(), timezone: 'INVALID');
    }

    /**
     * Test standard time difference formatting.
     */
    #[DataProvider('standardTimeDifferenceProvider')]
    #[Test]
    public function standardTimeDifference(int $seconds, string $expected): void
    {
        $timestampTo   = FrozenClock::fromUtc()->now()->setTimestamp(self::FrozenTimestamp);
        $timestampFrom = $timestampTo->getTimestamp() - $seconds;

        self::assertSame($expected, Dates::timeDifference($timestampFrom, $timestampTo->getTimestamp()));
    }

    /**
     * Test time difference with empty timezone (should default to UTC).
     */
    #[Test]
    public function timeDifferenceWithEmptyTimezone(): void
    {
        $timestampTo   = FrozenClock::fromUtc()->now();
        $timestampFrom = FrozenClock::fromUtc()->now()->getTimestamp() - 3_600;

        self::assertSame(
            '1 hour old',
            Dates::timeDifference($timestampFrom, $timestampTo->getTimestamp(), '')
        );
    }

    /**
     * Test Dates::timezoneInfo().
     */
    #[Test]
    public function timezoneInfo(): void
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
     * @param array{
     *     offset: int<-10, 2>,
     *     country: 'N/A',
     *     latitude: 'N/A',
     *     longitude: 'N/A',
     *     'dst': null
     * } $expected
     */
    #[DataProvider('falseForLocationProvider')]
    #[Test]
    public function timezoneInfoLocationReturnsFalse(array $expected, string $timezone): void
    {
        self::assertSame($expected, Dates::timezoneInfo($timezone, true));
    }

    /**
     * Test Dates::validTimezone().
     */
    #[Test]
    public function validTimezone(): void
    {
        self::assertFalse(Dates::validTimezone('InvalidTimezone'));
        self::assertTrue(Dates::validTimezone('America/New_York'));
    }

    /**
     * Data provider for extended time difference tests.
     *
     * @return iterable<string, array{int, string}>
     */
    public static function extendedTimeDifferenceProvider(): iterable
    {
        yield 'complex month' => [
            604_800 * 5,
            '1 month 4 days old',
        ];
        yield 'complex months' => [
            604_800 * 10,
            '2 months 2 weeks old',
        ];
        yield 'complex year' => [
            2_592_000 * 15,
            '1 year 2 months 4 weeks old',
        ];
        yield 'complex years' => [
            2_592_000 * 36,
            '2 years 11 months 3 weeks old',
        ];
        yield 'many complex years' => [
            2_592_000 * 140,
            '11 years 5 months 5 weeks old',
        ];
    }

    public static function falseForLocationProvider(): iterable
    {
        yield [['offset' => 1, 'country' => 'N/A', 'latitude' => 'N/A', 'longitude' => 'N/A', 'dst' => null], 'CET'];
        yield [['offset' => 2, 'country' => 'N/A', 'latitude' => 'N/A', 'longitude' => 'N/A', 'dst' => null], 'EET'];
        yield [['offset' => -5, 'country' => 'N/A', 'latitude' => 'N/A', 'longitude' => 'N/A', 'dst' => null], 'EST'];
        yield [['offset' => 0, 'country' => 'N/A', 'latitude' => 'N/A', 'longitude' => 'N/A', 'dst' => null], 'GMT'];
        yield [['offset' => 0, 'country' => 'N/A', 'latitude' => 'N/A', 'longitude' => 'N/A', 'dst' => null], 'GMT+0'];
        yield [['offset' => 0, 'country' => 'N/A', 'latitude' => 'N/A', 'longitude' => 'N/A', 'dst' => null], 'GMT-0'];
        yield [['offset' => -10, 'country' => 'N/A', 'latitude' => 'N/A', 'longitude' => 'N/A', 'dst' => null], 'HST'];
        yield [['offset' => 1, 'country' => 'N/A', 'latitude' => 'N/A', 'longitude' => 'N/A', 'dst' => null], 'MET'];
        yield [['offset' => -7, 'country' => 'N/A', 'latitude' => 'N/A', 'longitude' => 'N/A', 'dst' => null], 'MST'];
        yield [['offset' => 0, 'country' => 'N/A', 'latitude' => 'N/A', 'longitude' => 'N/A', 'dst' => null], 'UCT'];
        yield [['offset' => 0, 'country' => 'N/A', 'latitude' => 'N/A', 'longitude' => 'N/A', 'dst' => null], 'WET'];
    }

    public static function invalidTimestampProvider(): iterable
    {
        yield [0, 0];
        yield [0, 1_234_567];
        yield [1_234_567, 0];
        yield [0, \PHP_INT_MAX];
        yield [\PHP_INT_MAX, 0];
    }

    /**
     * Data provider for standard time difference tests.
     *
     * @return iterable<string, array{int, string}>
     */
    public static function standardTimeDifferenceProvider(): iterable
    {
        yield 'one second' => [1, '1 second old'];
        yield 'multiple seconds' => [15, '15 seconds old'];
        yield 'half minute' => [30, '30 seconds old'];
        yield 'one minute' => [60, '1 minute old'];
        yield 'multiple minutes' => [60 * 5, '5 minutes old'];
        yield 'one hour' => [3_600, '1 hour old'];
        yield 'multiple hours' => [3_600 * 2, '2 hours old'];
        yield 'one day' => [3_600 * 24, '1 day old'];
        yield 'multiple days' => [3_600 * 24 * 5, '5 days old'];
        yield 'one week' => [3_600 * 24 * 7, '1 week old'];
        yield 'multiple weeks' => [3_600 * 24 * 14, '2 weeks old'];
        yield 'one month' => [604_800 * 5, '1 month old'];
        yield 'multiple months' => [604_800 * 10, '2 months old'];
        yield 'one year' => [2_592_000 * 15, '1 year old'];
        yield 'multiple years' => [2_592_000 * 36, '2 years old'];
        yield 'many years' => [2_592_000 * 140, '11 years old'];
    }
}
