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

use InvalidArgumentException;
use Random\RandomException;
use ValueError;
use voku\helper\ASCII;

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
use function preg_quote;
use function preg_replace;
use function preg_replace_callback;
use function random_bytes;
use function str_contains;
use function str_ends_with;
use function str_replace;
use function str_split;
use function str_starts_with;
use function strcmp;
use function trim;

use const FILTER_VALIDATE_EMAIL;
use const JSON_ERROR_NONE;
use const MB_CASE_LOWER;
use const MB_CASE_TITLE;
use const MB_CASE_UPPER;

/**
 * String utilities.
 *
 * @see Tests\StringsTest
 */
abstract class Strings
{
    /**
     * Encoding to use for multibyte-based functions.
     *
     * @var string Encoding
     */
    private static string $encoding = 'UTF-8';

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
     * Returns current encoding.
     *
     * @return string Current encoding.
     */
    public static function getEncoding(): string
    {
        return self::$encoding;
    }

    /**
     * guid().
     *
     * Generate a Globally/Universally Unique Identifier (version 4).
     *
     * @throws RandomException
     *
     * @return string Random GUID.
     */
    public static function guid(): string
    {
        return \sprintf(
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
     * obscureEmail().
     *
     * Obscures an email address.
     *
     * @param string $email Email address to obscure.
     *
     * @throws InvalidArgumentException
     *
     * @return string Obscured email address.
     */
    public static function obscureEmail(string $email): string
    {
        // Sanity check
        if (!Strings::validEmail($email)) {
            throw new InvalidArgumentException('Invalid $email specified.');
        }

        // Split and process
        $email = array_map(
            static fn (string $char): string => '&#' . \ord($char) . ';',
            /** @scrutinizer ignore-type */
            str_split($email)
        );

        return implode('', $email);
    }

    /**
     * randomBytes().
     *
     * Generate cryptographically secure pseudo-random bytes.
     *
     * @param int<1, max> $length Length of the random string that should be returned in bytes.
     *
     * @throws RandomException
     * @throws ValueError
     *
     * @return string Random bytes of $length length.
     */
    public static function randomBytes(int $length): string
    {
        // Sanity check
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
     * @throws RandomException|ValueError
     *
     * @return string Random string of $length length.
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
     * substr().
     *
     * Returns the portion of string specified by the start and length parameters.
     *
     * @param string   $string The input string.
     * @param int      $start  Start position.
     * @param null|int $length Characters from $start.
     *
     * @return string Extracted part of the string.
     */
    public static function substr(string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length, (self::$encoding ?? null));
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
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * validJson().
     *
     * Determines if a string is valid JSON.
     *
     * @deprecated as of 2.0.0 and will be removed in v3.0
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
}
