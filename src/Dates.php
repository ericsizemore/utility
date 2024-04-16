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

namespace Esi\Utility;

use Esi\Clock\FrozenClock;
use Esi\Clock\SystemClock;
use Exception;
use InvalidArgumentException;
use RuntimeException;

use function ceil;
use function time;

/**
 * Date utilities.
 *
 * @see Tests\DatesTest
 */
final class Dates
{
    /**
     * Timezone default when one isn't provided.
     */
    public const DEFAULT_TIMEZONE = 'UTC';

    /**
     * Regex used to validate a given timestamp.
     *
     * @var string VALIDATE_TIMESTAMP_REGEX
     */
    public const VALIDATE_TIMESTAMP_REGEX = '/^\d{8,13}$/';

    /**
     * timeDifference().
     *
     * Formats the difference between two timestamps to be human-readable.
     *
     * @param int    $timestampFrom Starting unix timestamp.
     * @param int    $timestampTo   Ending unix timestamp.
     * @param string $timezone      The timezone to use. Must be a valid timezone:
     *                              http://www.php.net/manual/en/timezones.php
     * @param string $append        The string to append to the difference.
     *
     * @throws InvalidArgumentException|RuntimeException|Exception
     */
    public static function timeDifference(int $timestampFrom, int $timestampTo = 0, string $timezone = Dates::DEFAULT_TIMEZONE, string $append = ' old'): string
    {
        if ($timezone === '') {
            $timezone = Dates::DEFAULT_TIMEZONE;
        }

        if (!Dates::validTimezone($timezone)) {
            throw new RuntimeException('$timezone appears to be invalid.');
        }

        // Normalize timestamps
        $timestampTo   = (Dates::validateTimestamp($timestampTo)) ? $timestampTo : time();
        $timestampFrom = (Dates::validateTimestamp($timestampFrom)) ? $timestampFrom : time();

        // This will generally only happen if the $timestampFrom was 0, or if it's invalid, as it is set to time();
        // as is $timestampTo if left at 0
        if ($timestampFrom >= $timestampTo) {
            throw new InvalidArgumentException('$timestampFrom needs to be less than $timestampTo.');
        }

        // Create FrozenClock objects with timezone
        $timestampFrom = new FrozenClock(new \DateTimeImmutable('@' . $timestampFrom, new \DateTimeZone($timezone)));
        $timestampTo   = new FrozenClock(new \DateTimeImmutable('@' . $timestampTo, new \DateTimeZone($timezone)));

        // Calculate difference
        $difference = $timestampFrom->now()->diff($timestampTo->now());

        // Format the difference
        $string = match (true) {
            $difference->y > 0  => $difference->y . ' year(s)',
            $difference->m > 0  => $difference->m . ' month(s)',
            $difference->d >= 7 => ceil($difference->d / 7) . ' week(s)',
            $difference->d > 0  => $difference->d . ' day(s)',
            $difference->h > 0  => $difference->h . ' hour(s)',
            $difference->i > 0  => $difference->i . ' minute(s)',
            $difference->s > 0  => $difference->s . ' second(s)',
            default             => ''
        };

        return $string . $append;
    }

    /**
     * timezoneInfo().
     *
     * Retrieves information about a timezone.
     *
     * Note: Must be a valid timezone recognized by PHP.
     *
     * @see http://www.php.net/manual/en/timezones.php
     *
     * @param string $timezone The timezone to return information for.
     *
     * @return array<string, bool|float|int|string|null>
     *
     * @throws InvalidArgumentException|RuntimeException|Exception
     */
    public static function timezoneInfo(string $timezone = Dates::DEFAULT_TIMEZONE): array
    {
        if ($timezone === '') {
            $timezone = Dates::DEFAULT_TIMEZONE;
        }

        if (!Dates::validTimezone($timezone)) {
            throw new RuntimeException('$timezone appears to be invalid.');
        }

        $clock = (new SystemClock($timezone))->freeze();

        $location    = $clock->now()->getTimezone()->getLocation();
        $transitions = $clock->now()->getTimezone()->getTransitions(
            $clock->now()->getTimestamp(),
            $clock->now()->getTimestamp()
        );

        return [
            'offset'    => $clock->now()->getOffset() / 3_600,
            'country'   => $location['country_code'] ?? 'N/A',
            'latitude'  => $location['latitude'] ?? 'N/A',
            'longitude' => $location['longitude'] ?? 'N/A',
            'dst'       => $transitions[0]['isdst'] ?? null,
        ];
    }

    /**
     * Determines if a given timestamp matches the valid range that is typically
     * found in a unix timestamp (at least in PHP).
     *
     * Typically, a unix timestamp can be between 8 and 13 digits in length, and considered
     * valid if greater than 0.
     *
     * @param int $timestamp The timestamp to validate.
     */
    public static function validateTimestamp(int $timestamp): bool
    {
        if ($timestamp === 0 || $timestamp < 0 || $timestamp > PHP_INT_MAX) {
            return false;
        }

        return preg_match(self::VALIDATE_TIMESTAMP_REGEX, (string) $timestamp) === 1;
    }

    /**
     * Determines if a given timezone is valid, according to
     * {@link http://www.php.net/manual/en/timezones.php}.
     *
     * @param string $timezone The timezone to validate.
     */
    public static function validTimezone(string $timezone): bool
    {
        static $validTimezones;

        $validTimezones ??= \DateTimeZone::listIdentifiers();

        return Arrays::valueExists($validTimezones, $timezone);
    }
}
