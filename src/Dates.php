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

namespace Esi\Utility;

// Exceptions
use Exception;
use InvalidArgumentException;
use RuntimeException;

// Classes
use DateTimeZone;
use DateTime;

// Functions
use function ceil;
use function time;

/**
 * Date utilities.
 * @see \Esi\Utility\Tests\DatesTest
 */
final class Dates
{
    /**
     * Regex used to validate a given timestamp.
     *
     * @var string VALIDATE_TIMESTAMP_REGEX
     */
    public const VALIDATE_TIMESTAMP_REGEX = '/^\d{8,11}$/';

    /**
     * Timezone default when one isn't provided.
     */
    public const DEFAULT_TIMEZONE = 'UTC';

    /**
     * timeDifference()
     *
     * Formats the difference between two timestamps to be human-readable.
     *
     * @param   int     $timestampFrom  Starting unix timestamp.
     * @param   int     $timestampTo    Ending unix timestamp.
     * @param   string  $timezone       The timezone to use. Must be a valid timezone:
     *                                  {@see http://www.php.net/manual/en/timezones.php}
     * @param   string  $append         The string to append to the difference.
     *
     * @throws  InvalidArgumentException|RuntimeException|Exception
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

        // Create DateTime objects and set timezone
        $timestampFrom = (new DateTime('@' . $timestampFrom))->setTimezone(new DateTimeZone($timezone));
        $timestampTo   = (new DateTime('@' . $timestampTo))->setTimezone(new DateTimeZone($timezone));

        // Calculate difference
        $difference = $timestampFrom->diff($timestampTo);

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
     * timezoneInfo()
     *
     * Retrieves information about a timezone.
     *
     * Note: Must be a valid timezone recognized by PHP.
     *
     * @see http://www.php.net/manual/en/timezones.php
     *
     * @param   string  $timezone  The timezone to return information for.
     * @return  array<string, bool|float|int|string|null>
     *
     * @throws  InvalidArgumentException|RuntimeException|Exception
     */
    public static function timezoneInfo(string $timezone = Dates::DEFAULT_TIMEZONE): array
    {
        if ($timezone === '') {
            $timezone = Dates::DEFAULT_TIMEZONE;
        }

        if (!Dates::validTimezone($timezone)) {
            throw new RuntimeException('$timezone appears to be invalid.');
        }

        $dateTimeZone = new DateTimeZone($timezone);

        $location = $dateTimeZone->getLocation();

        return [
            'offset'    => $dateTimeZone->getOffset(new DateTime('now', new DateTimeZone('GMT'))) / 3_600,
            'country'   => $location['country_code'] ?? 'N/A',
            'latitude'  => $location['latitude'] ?? 'N/A',
            'longitude' => $location['longitude'] ?? 'N/A',
            'dst'       => $dateTimeZone->getTransitions(time(), time())[0]['isdst'] ?? null,
        ];
    }

    /**
     * Determines if a given timezone is valid, according to
     * {@link http://www.php.net/manual/en/timezones.php}.
     *
     * @param  string  $timezone  The timezone to validate.
     */
    public static function validTimezone(string $timezone): bool
    {
        static $validTimezones;

        $validTimezones ??= DateTimeZone::listIdentifiers();

        return Arrays::valueExists($validTimezones, $timezone);
    }

    /**
     * Determines if a given timestamp matches the valid range that is typically
     * found in a unix timestamp (at least in PHP).
     *
     * Typically, a timestamp for PHP can be valid if it is either 0 or between 8 and 11 digits in length.
     *
     * @param  int  $timestamp  The timestamp to validate.
     */
    public static function validateTimestamp(int $timestamp): bool
    {
        if ($timestamp === 0 || $timestamp < 0) {
            return false;
        }
        return (preg_match(self::VALIDATE_TIMESTAMP_REGEX, (string) $timestamp) === 1);
    }
}
