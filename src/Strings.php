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
use function ltrim;
use function mb_convert_case;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
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

use const FILTER_VALIDATE_EMAIL;
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
     * Map of language code => \voku\helper\ASCII constant, of the
     * language codes supported by it.
     *
     * @var array<string, string>
     */
    private const LANGUAGE_MAP = [
        'uz'                => ASCII::UZBEK_LANGUAGE_CODE,
        'tk'                => ASCII::TURKMEN_LANGUAGE_CODE,
        'th'                => ASCII::THAI_LANGUAGE_CODE,
        'ps'                => ASCII::PASHTO_LANGUAGE_CODE,
        'or'                => ASCII::ORIYA_LANGUAGE_CODE,
        'mn'                => ASCII::MONGOLIAN_LANGUAGE_CODE,
        'ko'                => ASCII::KOREAN_LANGUAGE_CODE,
        'ky'                => ASCII::KIRGHIZ_LANGUAGE_CODE,
        'hy'                => ASCII::ARMENIAN_LANGUAGE_CODE,
        'bn'                => ASCII::BENGALI_LANGUAGE_CODE,
        'be'                => ASCII::BELARUSIAN_LANGUAGE_CODE,
        'am'                => ASCII::AMHARIC_LANGUAGE_CODE,
        'ja'                => ASCII::JAPANESE_LANGUAGE_CODE,
        'zh'                => ASCII::CHINESE_LANGUAGE_CODE,
        'nl'                => ASCII::DUTCH_LANGUAGE_CODE,
        'it'                => ASCII::ITALIAN_LANGUAGE_CODE,
        'mk'                => ASCII::MACEDONIAN_LANGUAGE_CODE,
        'pt'                => ASCII::PORTUGUESE_LANGUAGE_CODE,
        'el__greeklish'     => ASCII::GREEKLISH_LANGUAGE_CODE,
        'el'                => ASCII::GREEK_LANGUAGE_CODE,
        'hi'                => ASCII::HINDI_LANGUAGE_CODE,
        'sv'                => ASCII::SWEDISH_LANGUAGE_CODE,
        'tr'                => ASCII::TURKISH_LANGUAGE_CODE,
        'bg'                => ASCII::BULGARIAN_LANGUAGE_CODE,
        'hu'                => ASCII::HUNGARIAN_LANGUAGE_CODE,
        'my'                => ASCII::MYANMAR_LANGUAGE_CODE,
        'hr'                => ASCII::CROATIAN_LANGUAGE_CODE,
        'fi'                => ASCII::FINNISH_LANGUAGE_CODE,
        'ka'                => ASCII::GEORGIAN_LANGUAGE_CODE,
        'ru'                => ASCII::RUSSIAN_LANGUAGE_CODE,
        'ru__passport_2013' => ASCII::RUSSIAN_PASSPORT_2013_LANGUAGE_CODE,
        'ru__gost_2000_b'   => ASCII::RUSSIAN_GOST_2000_B_LANGUAGE_CODE,
        'uk'                => ASCII::UKRAINIAN_LANGUAGE_CODE,
        'kk'                => ASCII::KAZAKH_LANGUAGE_CODE,
        'cs'                => ASCII::CZECH_LANGUAGE_CODE,
        'da'                => ASCII::DANISH_LANGUAGE_CODE,
        'pl'                => ASCII::POLISH_LANGUAGE_CODE,
        'ro'                => ASCII::ROMANIAN_LANGUAGE_CODE,
        'eo'                => ASCII::ESPERANTO_LANGUAGE_CODE,
        'et'                => ASCII::ESTONIAN_LANGUAGE_CODE,
        'lv'                => ASCII::LATVIAN_LANGUAGE_CODE,
        'lt'                => ASCII::LITHUANIAN_LANGUAGE_CODE,
        'no'                => ASCII::NORWEGIAN_LANGUAGE_CODE,
        'vi'                => ASCII::VIETNAMESE_LANGUAGE_CODE,
        'ar'                => ASCII::ARABIC_LANGUAGE_CODE,
        'fa'                => ASCII::PERSIAN_LANGUAGE_CODE,
        'sr'                => ASCII::SERBIAN_LANGUAGE_CODE,
        'sr__cyr'           => ASCII::SERBIAN_CYRILLIC_LANGUAGE_CODE,
        'sr__lat'           => ASCII::SERBIAN_LATIN_LANGUAGE_CODE,
        'az'                => ASCII::AZERBAIJANI_LANGUAGE_CODE,
        'sk'                => ASCII::SLOVAK_LANGUAGE_CODE,
        'fr'                => ASCII::FRENCH_LANGUAGE_CODE,
        'fr_at'             => ASCII::FRENCH_AUSTRIAN_LANGUAGE_CODE,
        'fr_ch'             => ASCII::FRENCH_SWITZERLAND_LANGUAGE_CODE,
        'de'                => ASCII::GERMAN_LANGUAGE_CODE,
        'de_at'             => ASCII::GERMAN_AUSTRIAN_LANGUAGE_CODE,
        'de_ch'             => ASCII::GERMAN_SWITZERLAND_LANGUAGE_CODE,
        'en'                => ASCII::ENGLISH_LANGUAGE_CODE,
        'latin'             => ASCII::EXTRA_LATIN_CHARS_LANGUAGE_CODE,
        'msword'            => ASCII::EXTRA_MSWORD_CHARS_LANGUAGE_CODE,
    ];

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
     * @param string                        $value    Value to transliterate.
     * @param key-of<Strings::LANGUAGE_MAP> $language Language code (2 characters, eg: en). {@see ASCII}
     *
     * @return string Value as ASCII.
     */
    public static function ascii(string $value, string $language = 'en'): string
    {
        if (!Arrays::keyExists(Strings::LANGUAGE_MAP, $language)) {
            $language = Strings::LANGUAGE_MAP['en'];
        } else {
            $language = Strings::LANGUAGE_MAP[$language];
        }

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
        return mb_convert_case($value, MB_CASE_LOWER, self::$encoding);
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
     * @param int<0, max> $length Length of the random string that should be returned in bytes.
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
        return mb_substr($string, $start, $length, self::$encoding);
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
        return mb_convert_case($value, MB_CASE_TITLE, self::$encoding);
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
        return mb_convert_case($value, MB_CASE_UPPER, self::$encoding);
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
}
