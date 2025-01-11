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

namespace Esi\Utility;

use Esi\Clock\SystemClock;
use Exception;
use InvalidArgumentException;
use RuntimeException;

use function ceil;
use function rtrim;

/**
 * Date utilities.
 *
 * @see Tests\DatesTest
 */
abstract class Dates
{
    /**
     * Timezone default when one isn't provided.
     */
    public const DEFAULT_TIMEZONE = 'UTC';

    /**
     * The interval units we use from \DateInterval when determining time difference.
     */
    public const INTERVAL_UNITS = [
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];

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
     * @param int    $timestampFrom  Starting unix timestamp.
     * @param int    $timestampTo    Ending unix timestamp.
     * @param string $timezone       The timezone to use. Must be a valid timezone:
     *                               https://www.php.net/manual/en/timezones.php
     * @param string $append         The string to append to the difference.
     * @param bool   $extendedOutput By default, the time difference will be the first non-zero unit.
     *                               Set this option to true and it will display the full output considering
     *                               all units. For example: 2 days 2 hours 20 minutes old
     *
     * @throws Exception|InvalidArgumentException|RuntimeException
     */
    public static function timeDifference(
        int $timestampFrom,
        int $timestampTo = 0,
        string $timezone = Dates::DEFAULT_TIMEZONE,
        string $append = ' old',
        bool $extendedOutput = false
    ): string {
        if ($timezone === '') {
            $timezone = Dates::DEFAULT_TIMEZONE;
        }

        if (!Dates::validTimezone($timezone)) {
            throw new RuntimeException('$timezone appears to be invalid.');
        }

        // Create FrozenClock with timezone
        $frozenClock = (new SystemClock(new \DateTimeZone($timezone)))->freeze();

        // Normalize timestamps
        $timestampTo   = (Dates::validateTimestamp($timestampTo)) ? $timestampTo : $frozenClock->now()->getTimestamp();
        $timestampFrom = (Dates::validateTimestamp($timestampFrom)) ? $timestampFrom : $frozenClock->now()->getTimestamp();

        // This will generally only happen if the $timestampFrom was 0, or if it's invalid, as it is set to time();
        // as is $timestampTo if left at 0
        if ($timestampFrom >= $timestampTo) {
            throw new InvalidArgumentException('$timestampFrom needs to be less than $timestampTo.');
        }

        // Calculate difference
        $timestampFrom = $frozenClock->now()->setTimestamp($timestampFrom);
        $timestampTo   = $frozenClock->now()->setTimestamp($timestampTo);
        $difference    = $timestampFrom->diff($timestampTo);

        // Format the difference
        $string = Dates::formatDifferenceOutput($difference, $extendedOutput);

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
     * @param string $timezone       The timezone to return information for.
     * @param bool   $includeBcZones Whether to include backwards compatible time zones.
     *
     * @throws Exception|InvalidArgumentException|RuntimeException
     *
     * @return array{
     *     offset: float|int,
     *     country: string,
     *     latitude: float|string,
     *     longitude: float|string,
     *     dst: null|bool
     * }
     */
    public static function timezoneInfo(string $timezone = Dates::DEFAULT_TIMEZONE, bool $includeBcZones = false): array
    {
        if ($timezone === '') {
            $timezone = Dates::DEFAULT_TIMEZONE;
        }

        if (!Dates::validTimezone($timezone, $includeBcZones)) {
            throw new RuntimeException('$timezone appears to be invalid.');
        }

        $frozenClock = (new SystemClock($timezone))->freeze();

        $location = $frozenClock->now()->getTimezone()->getLocation();

        // If getting the location fails
        if ($location === false) {
            $location = [
                'country_code' => 'N/A',
                'latitude'     => 'N/A',
                'longitude'    => 'N/A',
            ];
        }

        $transitions = $frozenClock->now()->getTimezone()->getTransitions(
            $frozenClock->now()->getTimestamp(),
            $frozenClock->now()->getTimestamp()
        );

        /**
         * @var array{country_code?: string, latitude?: float|string, longitude?: float|string} $location
         */

        return [
            'offset'    => $frozenClock->now()->getOffset() / 3_600,
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
        if ($timestamp === 0 || $timestamp < 0 || $timestamp > \PHP_INT_MAX) {
            return false;
        }

        return preg_match(self::VALIDATE_TIMESTAMP_REGEX, (string) $timestamp) === 1;
    }

    /**
     * Determines if a given timezone is valid, according to
     * {@link https://www.php.net/manual/en/timezones.php}.
     *
     * @param string $timezone       The timezone to validate.
     * @param bool   $includeBcZones Whether to include backwards compatible time zones.
     */
    public static function validTimezone(string $timezone, bool $includeBcZones = false): bool
    {
        /**
         * @var null|list<string>
         */
        static $validTimezones = null;
        /**
         * @var null|list<string>
         */
        static $validTimezonesBc = null;

        $validTimezones ??= \DateTimeZone::listIdentifiers();

        if ($includeBcZones) {
            $validTimezonesBc ??= \DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC);

            /**
             * @var list<string> $validTimezonesBc
             */
            return Arrays::valueExists($validTimezonesBc, $timezone);
        }

        /**
         * @var list<string> $validTimezones
         */
        return Arrays::valueExists($validTimezones, $timezone);
    }

    /**
     * Helper function for timeDifference.
     *
     * @see self::timeDifference()
     */
    private static function formatDifferenceOutput(\DateInterval $dateInterval, bool $extendedOutput): string
    {
        $string = '';

        foreach (Dates::INTERVAL_UNITS as $unit => $unitName) {
            /**
             * @var int $property
             */
            $property = $dateInterval->$unit;

            if ($property === 0) {
                continue;
            }

            if ($unit === 'd' && $property >= 7) {
                $property = ceil($property / 7);
                $unitName = 'week';
            }

            $unitName .= ($property > 1) ? 's' : '';

            if ($extendedOutput) {
                $string .= \sprintf('%d %s ', $property, $unitName);
            } else {
                $string = \sprintf('%d %s ', $property, $unitName);
                break;
            }
        }

        return rtrim($string);
    }
}
