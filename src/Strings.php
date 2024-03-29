<?php

declare(strict_types=1);

/**
 * Utility - Collection of various PHP utility functions.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 *
 * @version   2.0.0
 *
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
use InvalidArgumentException;
use Random\RandomException;
use ValueError;
use voku\helper\ASCII;

// Functions
use function array_map;
use function bin2hex;
use function filter_var;
use function implode;
use function json_decode;
use function json_last_error;
use function ltrim;
use function mb_convert_case;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function ord;
use function preg_quote;
use function preg_replace;
use function preg_replace_callback;
use function random_bytes;
use function sprintf;
use function str_contains;
use function str_ends_with;
use function str_replace;
use function str_split;
use function str_starts_with;
use function strcmp;
use function trim;

// Constants
use const FILTER_VALIDATE_EMAIL;
use const JSON_ERROR_NONE;
use const MB_CASE_LOWER;
use const MB_CASE_TITLE;
use const MB_CASE_UPPER;

/**
 * String utilities.
 *
 * @see \Esi\Utility\Tests\StringsTest
 */
final class Strings
{
    /**
     * Encoding to use for multibyte-based functions.
     *
     * @var string Encoding
     */
    private static string $encoding = 'UTF-8';

    /**
     * Returns current encoding.
     *
     * @return string Current encoding.
     */
    public static function getEncoding(): string
    {
        return self::$encoding;
    }

    /**
     * Sets the encoding to use for multibyte-based functions.
     *
     * @param string $newEncoding Charset.
     * @param bool   $iniUpdate   Update php.ini's default_charset?
     */
    public static function setEncoding(string $newEncoding = '', bool $iniUpdate = false): void
    {
        if ($newEncoding !== '') {
            self::$encoding = $newEncoding;
        }

        if ($iniUpdate) {
            Environment::iniSet('default_charset', self::$encoding);
            Environment::iniSet('internal_encoding', self::$encoding);
        }
    }

    /**
     * title().
     *
     * Convert the given string to title case.
     *
     * @param string $value Value to convert.
     *
     * @return string Value converted to titlecase.
     */
    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, (self::$encoding ?? null));
    }

    /**
     * lower().
     *
     * Convert the given string to lower case.
     *
     * @param string $value Value to convert.
     *
     * @return string Value converted to lowercase.
     */
    public static function lower(string $value): string
    {
        return mb_convert_case($value, MB_CASE_LOWER, (self::$encoding ?? null));
    }

    /**
     * upper().
     *
     * Convert the given string to upper case.
     *
     * @param string $value Value to convert.
     *
     * @return string Value converted to uppercase.
     */
    public static function upper(string $value): string
    {
        return mb_convert_case($value, MB_CASE_UPPER, (self::$encoding ?? null));
    }

    /**
     * substr().
     *
     * Returns the portion of string specified by the start and length parameters.
     *
     * @param string   $string The input string.
     * @param int      $start  Start position.
     * @param int|null $length Characters from $start.
     *
     * @return string Extracted part of the string.
     */
    public static function substr(string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length, (self::$encoding ?? null));
    }

    /**
     * lcfirst().
     *
     * Convert the first character of a given string to lower case.
     *
     * @since   1.0.1
     *
     * @param string $string The input string.
     *
     * @return string String with the first letter lowercase'd.
     */
    public static function lcfirst(string $string): string
    {
        return self::lower(self::substr($string, 0, 1)) . self::substr($string, 1);
    }

    /**
     * ucfirst().
     *
     * Convert the first character of a given string to upper case.
     *
     * @since   1.0.1
     *
     * @param string $string The input string.
     *
     * @return string String with the first letter uppercase'd.
     */
    public static function ucfirst(string $string): string
    {
        return self::upper(self::substr($string, 0, 1)) . self::substr($string, 1);
    }

    /**
     * Compares multibyte input strings in a binary safe case-insensitive manner.
     *
     * @since  1.0.1
     *
     * @param string $str1 The first string.
     * @param string $str2 The second string.
     *
     * @return int Returns < 0 if $str1 is less than $str2; > 0 if $str1
     *             is greater than $str2, and 0 if they are equal.
     */
    public static function strcasecmp(string $str1, string $str2): int
    {
        return strcmp(Strings::upper($str1), Strings::upper($str2));
    }

    /**
     * beginsWith().
     *
     * Determine if a string begins with another string.
     *
     * @param string $haystack    String to search in.
     * @param string $needle      String to check for.
     * @param bool   $insensitive True to do a case-insensitive search.
     * @param bool   $multibyte   True to perform checks via mbstring, false otherwise.
     *
     * @return bool True if the string begins with $needle, false otherwise.
     */
    public static function beginsWith(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool
    {
        if ($insensitive) {
            $haystack = Strings::lower($haystack);
            $needle   = Strings::lower($needle);
        }

        return (
            $multibyte
            ? mb_strpos($haystack, $needle) === 0
            : str_starts_with($haystack, $needle)
        );
    }

    /**
     * endsWith().
     *
     * Determine if a string ends with another string.
     *
     * @param string $haystack    String to search in.
     * @param string $needle      String to check for.
     * @param bool   $insensitive True to do a case-insensitive search.
     * @param bool   $multibyte   True to perform checks via mbstring, false otherwise.
     *
     * @return bool True if the string ends with $needle, false otherwise.
     */
    public static function endsWith(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool
    {
        if ($insensitive) {
            $haystack = Strings::lower($haystack);
            $needle   = Strings::lower($needle);
        }

        return (
            $multibyte
            ? Strings::substr($haystack, -Strings::length($needle)) === $needle
            : str_ends_with($haystack, $needle)
        );
    }

    /**
     * doesContain().
     *
     * Determine if a string exists within another string.
     *
     * @param string $haystack    String to search in.
     * @param string $needle      String to check for.
     * @param bool   $insensitive True to do a case-insensitive search.
     * @param bool   $multibyte   True to perform checks via mbstring, false otherwise.
     *
     * @return bool True if the string contains $needle, false otherwise.
     */
    public static function doesContain(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool
    {
        if ($insensitive) {
            $haystack = Strings::lower($haystack);
            $needle   = Strings::lower($needle);
        }

        return (
            $multibyte
            ? mb_strpos($haystack, $needle) !== false
            : str_contains($haystack, $needle)
        );
    }

    /**
     * doesNotContain().
     *
     * Determine if a string does not exist within another string.
     *
     * @param string $haystack    String to search in.
     * @param string $needle      String to check for.
     * @param bool   $insensitive True to do a case-insensitive search.
     * @param bool   $multibyte   True to perform checks via mbstring, false otherwise.
     *
     * @return bool True if the string does not contain $needle, false otherwise.
     */
    public static function doesNotContain(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool
    {
        return (!Strings::doesContain($haystack, $needle, $insensitive, $multibyte));
    }

    /**
     * length().
     *
     * Get string length.
     *
     * @param string $string     The string being checked for length.
     * @param bool   $binarySafe Forces '8bit' encoding so that the length check is binary safe.
     *
     * @return int The length of the given string.
     */
    public static function length(string $string, bool $binarySafe = false): int
    {
        return mb_strlen($string, ($binarySafe ? '8bit' : self::$encoding));
    }

    /**
     * camelCase().
     *
     * Returns a camelCase version of the string.
     *
     * Shoutout to Daniel St. Jules (https://github.com/danielstjules/Stringy/) for
     * inspiration for this function. This function is based on Stringy/camelize().
     *
     * My changes were mainly to make it fit into Utility.
     *
     * @since 2.0.0
     *
     * @param string $string String to camelCase.
     *
     * @return string camelCase'd string.
     */
    public static function camelCase(string $string): string
    {
        // Trim surrounding spaces
        $string = trim($string);
        $string = self::lcfirst($string);

        // Remove leading dashes and underscores
        $string = ltrim($string, '-_');

        // Transformation
        $transformation = (string) preg_replace_callback(
            '/[-_\s]+(.)?/u',
            static fn (array $match): string => isset($match[1]) ? self::upper($match[1]) : '',
            $string
        );

        return (string) preg_replace_callback('/\p{N}+(.)?/u', static fn (array $match): string => self::upper($match[0]), $transformation);
    }

    /**
     * ascii().
     *
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param string $value    Value to transliterate.
     * @param string $language Language code (2 characters, eg: en). {@see ASCII}
     *
     * @return string Value as ASCII.
     */
    public static function ascii(string $value, string $language = 'en'): string
    {
        /** @var ASCII::*_LANGUAGE_CODE $language */
        return ASCII::to_ascii($value, $language);
    }

    /**
     * slugify().
     *
     * Transforms a string into a URL or filesystem-friendly string.
     *
     * Note: Adapted from Illuminate/Support/Str::slug.
     *
     * @see https://packagist.org/packages/illuminate/support#v6.20.44
     * * @see http://opensource.org/licenses/MIT
     *
     * @param string  $title     String to convert.
     * @param string  $separator Separator used to separate words in $title.
     * @param ?string $language  Language code (2 characters, eg: en). {@see ASCII}
     *
     * @return string Transformed string.
     */
    public static function slugify(string $title, string $separator = '-', ?string $language = 'en'): string
    {
        $title = $language !== null ? Strings::ascii($title) : $title;

        // Replace @ with the word 'at'
        $title = str_replace('@', $separator . 'at' . $separator, $title);

        $title = (string) preg_replace('![' . preg_quote(($separator === '-' ? '_' : '-')) . ']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = (string) preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', Strings::lower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = (string) preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

        // Cleanup $title
        return trim($title, $separator);
    }

    /**
     * randomBytes().
     *
     * Generate cryptographically secure pseudo-random bytes.
     *
     * @param int<1, max> $length Length of the random string that should be returned in bytes.
     *
     * @return string Random bytes of $length length.
     *
     * @throws RandomException
     * @throws ValueError
     */
    public static function randomBytes(int $length): string
    {
        // Sanity check
        // @phpstan-ignore-next-line
        if ($length < 1) {
            throw new RandomException('$length must be greater than 1.');
        }

        // Generate bytes
        return random_bytes($length);
    }

    /**
     * randomString().
     *
     * Generates a secure random string, based on {@see static::randomBytes()}.
     *
     * @param int<min, max> $length Length the random string should be.
     *
     * @return string Random string of $length length.
     *
     * @throws RandomException | ValueError
     */
    public static function randomString(int $length = 8): string
    {
        // Sanity check
        if ($length < 1) {
            throw new RandomException('$length must be greater than 1.');
        }

        // Convert bytes to hexadecimal and truncate to the desired length
        return Strings::substr(bin2hex(Strings::randomBytes($length * 2)), 0, $length);
    }

    /**
     * validEmail().
     *
     * Validate an email address using PHP's built-in filter.
     *
     * @param string $email Value to check.
     *
     * @return bool True if the email is valid, false otherwise.
     */
    public static function validEmail(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * validJson().
     *
     * Determines if a string is valid JSON.
     *
     * @deprecated as of 2.0.0
     *
     * @param string $data The string to validate as JSON.
     *
     * @return bool True if the json appears to be valid, false otherwise.
     */
    public static function validJson(string $data): bool
    {
        $data = trim($data);
        json_decode($data);

        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * obscureEmail().
     *
     * Obscures an email address.
     *
     * @param string $email Email address to obscure.
     *
     * @return string Obscured email address.
     *
     * @throws InvalidArgumentException
     */
    public static function obscureEmail(string $email): string
    {
        // Sanity check
        if (!Strings::validEmail($email)) {
            throw new InvalidArgumentException('Invalid $email specified.');
        }

        // Split and process
        $email = array_map(
            static fn (string $char): string => '&#' . ord($char) . ';',
            /** @scrutinizer ignore-type */
            str_split($email)
        );

        return implode('', $email);
    }

    /**
     * guid().
     *
     * Generate a Globally/Universally Unique Identifier (version 4).
     *
     * @return string Random GUID.
     *
     * @throws RandomException
     */
    public static function guid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            Numbers::random(0, 0xff_ff),
            Numbers::random(0, 0xff_ff),
            Numbers::random(0, 0xff_ff),
            Numbers::random(0, 0x0f_ff) | 0x40_00,
            Numbers::random(0, 0x3f_ff) | 0x80_00,
            Numbers::random(0, 0xff_ff),
            Numbers::random(0, 0xff_ff),
            Numbers::random(0, 0xff_ff)
        );
    }
}
