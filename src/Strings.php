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
use InvalidArgumentException;
use Random\RandomException;
use ValueError;

// Functions
use function mb_convert_case;
use function mb_substr;
use function strcmp;
use function mb_strpos;
use function str_starts_with;
use function str_ends_with;
use function str_contains;
use function mb_strlen;
use function str_replace;
use function preg_replace;
use function preg_quote;
use function trim;
use function random_bytes;
use function bin2hex;
use function filter_var;
use function json_decode;
use function json_last_error;
use function array_map;
use function ord;
use function implode;
use function str_split;
use function sprintf;
use function preg_replace_callback;
use function ltrim;

// Constants
use const MB_CASE_TITLE;
use const MB_CASE_LOWER;
use const MB_CASE_UPPER;
use const FILTER_VALIDATE_EMAIL;
use const JSON_ERROR_NONE;

/**
 * String utilities.
 */
final class Strings
{
    /**
     * Encoding to use for multibyte-based functions.
     *
     * @var  string  Encoding
     */
    private static string $encoding = 'UTF-8';

    /**
     * Returns current encoding.
     *
     */
    public static function getEncoding(): string
    {
        return self::$encoding;
    }

    /**
     * Sets the encoding to use for multibyte-based functions.
     *
     * @param  string  $newEncoding  Charset.
     * @param  bool    $iniUpdate    Update php.ini's default_charset?
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
     * title()
     *
     * Convert the given string to title case.
     *
     * @param   string  $value  Value to convert.
     * @return  string
     */
    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, (self::$encoding ?? null));
    }

    /**
     * lower()
     *
     * Convert the given string to lower case.
     *
     * @param   string  $value  Value to convert.
     * @return  string
     */
    public static function lower(string $value): string
    {
        return mb_convert_case($value, MB_CASE_LOWER, (self::$encoding ?? null));
    }

    /**
     * upper()
     *
     * Convert the given string to upper case.
     *
     * @param   string  $value  Value to convert.
     * @return  string
     */
    public static function upper(string $value): string
    {
        return mb_convert_case($value, MB_CASE_UPPER, (self::$encoding ?? null));
    }

    /**
     * substr()
     *
     * Returns the portion of string specified by the start and length parameters.
     *
     * @param   string    $string  The input string.
     * @param   int       $start   Start position.
     * @param   int|null  $length  Characters from $start.
     * @return  string             Extracted part of the string.
     */
    public static function substr(string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length, (self::$encoding ?? null));
    }

    /**
     * lcfirst()
     *
     * Convert the first character of a given string to lower case.
     *
     * @since   1.0.1
     *
     * @param   string  $string  The input string.
     * @return  string
     */
    public static function lcfirst(string $string): string
    {
        return self::lower(self::substr($string, 0, 1)) . self::substr($string, 1);
    }

    /**
     * ucfirst()
     *
     * Convert the first character of a given string to upper case.
     *
     * @since   1.0.1
     *
     * @param   string  $string  The input string.
     * @return  string
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
     * @param  string  $str1  The first string.
     * @param  string  $str2  The second string.
     * @return int            Returns < 0 if $str1 is less than $str2; > 0 if $str1
     *                        is greater than $str2, and 0 if they are equal.
     */
    public static function strcasecmp(string $str1, string $str2): int
    {
        return strcmp(Strings::upper($str1), Strings::upper($str2));
    }

    /**
     * beginsWith()
     *
     * Determine if a string begins with another string.
     *
     * @param   string  $haystack     String to search in.
     * @param   string  $needle       String to check for.
     * @param   bool    $insensitive  True to do a case-insensitive search.
     * @param   bool    $multibyte    True to perform checks via mbstring, false otherwise.
     * @return  bool
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
     * endsWith()
     *
     * Determine if a string ends with another string.
     *
     * @param   string  $haystack     String to search in.
     * @param   string  $needle       String to check for.
     * @param   bool    $insensitive  True to do a case-insensitive search.
     * @param   bool    $multibyte    True to perform checks via mbstring, false otherwise.
     * @return  bool
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
     * doesContain()
     *
     * Determine if a string exists within another string.
     *
     * @param   string  $haystack     String to search in.
     * @param   string  $needle       String to check for.
     * @param   bool    $insensitive  True to do a case-insensitive search.
     * @param   bool    $multibyte    True to perform checks via mbstring, false otherwise.
     * @return  bool
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
     * doesNotContain()
     *
     * Determine if a string does not exist within another string.
     *
     * @param   string  $haystack     String to search in.
     * @param   string  $needle       String to check for.
     * @param   bool    $insensitive  True to do a case-insensitive search.
     * @param   bool    $multibyte    True to perform checks via mbstring, false otherwise.
     * @return  bool
     */
    public static function doesNotContain(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool
    {
        return (!Strings::doesContain($haystack, $needle, $insensitive, $multibyte));
    }

    /**
     * length()
     *
     * Get string length.
     *
     * @param   string  $string      The string being checked for length.
     * @param   bool    $binarySafe  Forces '8bit' encoding so that the length check is binary safe.
     * @return  int
     */
    public static function length(string $string, bool $binarySafe = false): int
    {
        return mb_strlen($string, ($binarySafe ? '8bit' : self::$encoding));
    }

    /**
     * camelCase()
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
     * @param  string  $string  String to camelCase.
     * @return string           camelCase'd string.
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
     * ascii()
     *
     * Transliterate a UTF-8 value to ASCII.
     *
     * Note: Adapted from Illuminate/Support/Str.
     *
     * @see https://packagist.org/packages/laravel/lumen-framework < v5.5
     * @see http://opensource.org/licenses/MIT
     *
     * @param   string  $value  Value to transliterate.
     * @return  string
     */
    public static function ascii(string $value): string
    {
        foreach (Strings::charMap() as $key => $val) {
            $value = str_replace($key, $val, $value);
        }
        return (string) preg_replace('/[^\x20-\x7E]/u', '', $value);
    }

    /**
     * charMap()
     *
     * Returns the replacements for the ascii method.
     *
     * @return  array<string, string>
     */
    private static function charMap(): array
    {
        static $charMap;

        return $charMap ??= [
            'Ǎ' => 'A', 'А' => 'A', 'Ā' => 'A', 'Ă' => 'A', 'Ą' => 'A', 'Å' => 'A',
            'Ǻ' => 'A', 'Ä' => 'Ae', 'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A',
            'Æ' => 'AE', 'Ǽ' => 'AE', 'Б' => 'B', 'Ç' => 'C', 'Ć' => 'C', 'Ĉ' => 'C',
            'Č' => 'C', 'Ċ' => 'C', 'Ц' => 'C', 'Ч' => 'Ch', 'Ð' => 'Dj', 'Đ' => 'Dj',
            'Ď' => 'Dj', 'Д' => 'Dj', 'É' => 'E', 'Ę' => 'E', 'Ё' => 'E', 'Ė' => 'E',
            'Ê' => 'E', 'Ě' => 'E', 'Ē' => 'E', 'È' => 'E', 'Е' => 'E', 'Э' => 'E',
            'Ë' => 'E', 'Ĕ' => 'E', 'Ф' => 'F', 'Г' => 'G', 'Ģ' => 'G', 'Ġ' => 'G',
            'Ĝ' => 'G', 'Ğ' => 'G', 'Х' => 'H', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ï' => 'I',
            'Ĭ' => 'I', 'İ' => 'I', 'Į' => 'I', 'Ī' => 'I', 'Í' => 'I', 'Ì' => 'I',
            'И' => 'I', 'Ǐ' => 'I', 'Ĩ' => 'I', 'Î' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J',
            'Й' => 'J', 'Я' => 'Ja', 'Ю' => 'Ju', 'К' => 'K', 'Ķ' => 'K', 'Ĺ' => 'L',
            'Л' => 'L', 'Ł' => 'L', 'Ŀ' => 'L', 'Ļ' => 'L', 'Ľ' => 'L', 'М' => 'M',
            'Н' => 'N', 'Ń' => 'N', 'Ñ' => 'N', 'Ņ' => 'N', 'Ň' => 'N', 'Ō' => 'O',
            'О' => 'O', 'Ǿ' => 'O', 'Ǒ' => 'O', 'Ơ' => 'O', 'Ŏ' => 'O', 'Ő' => 'O',
            'Ø' => 'O', 'Ö' => 'Oe', 'Õ' => 'O', 'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O',
            'Œ' => 'OE', 'П' => 'P', 'Ŗ' => 'R', 'Р' => 'R', 'Ř' => 'R', 'Ŕ' => 'R',
            'Ŝ' => 'S', 'Ş' => 'S', 'Š' => 'S', 'Ș' => 'S', 'Ś' => 'S', 'С' => 'S',
            'Ш' => 'Sh', 'Щ' => 'Shch', 'Ť' => 'T', 'Ŧ' => 'T', 'Ţ' => 'T', 'Ț' => 'T',
            'Т' => 'T', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
            'Ū' => 'U', 'Ǜ' => 'U', 'Ǚ' => 'U', 'Ù' => 'U', 'Ú' => 'U', 'Ü' => 'Ue',
            'Ǘ' => 'U', 'Ǖ' => 'U', 'У' => 'U', 'Ư' => 'U', 'Ǔ' => 'U', 'Û' => 'U',
            'В' => 'V', 'Ŵ' => 'W', 'Ы' => 'Y', 'Ŷ' => 'Y', 'Ý' => 'Y', 'Ÿ' => 'Y',
            'Ź' => 'Z', 'З' => 'Z', 'Ż' => 'Z', 'Ž' => 'Z', 'Ж' => 'Zh', 'á' => 'a',
            'ă' => 'a', 'â' => 'a', 'à' => 'a', 'ā' => 'a', 'ǻ' => 'a', 'å' => 'a',
            'ä' => 'ae', 'ą' => 'a', 'ǎ' => 'a', 'ã' => 'a', 'а' => 'a', 'ª' => 'a',
            'æ' => 'ae', 'ǽ' => 'ae', 'б' => 'b', 'č' => 'c', 'ç' => 'c', 'ц' => 'c',
            'ċ' => 'c', 'ĉ' => 'c', 'ć' => 'c', 'ч' => 'ch', 'ð' => 'dj', 'ď' => 'dj',
            'д' => 'dj', 'đ' => 'dj', 'э' => 'e', 'é' => 'e', 'ё' => 'e', 'ë' => 'e',
            'ê' => 'e', 'е' => 'e', 'ĕ' => 'e', 'è' => 'e', 'ę' => 'e', 'ě' => 'e',
            'ė' => 'e', 'ē' => 'e', 'ƒ' => 'f', 'ф' => 'f', 'ġ' => 'g', 'ĝ' => 'g',
            'ğ' => 'g', 'г' => 'g', 'ģ' => 'g', 'х' => 'h', 'ĥ' => 'h', 'ħ' => 'h',
            'ǐ' => 'i', 'ĭ' => 'i', 'и' => 'i', 'ī' => 'i', 'ĩ' => 'i', 'į' => 'i',
            'ı' => 'i', 'ì' => 'i', 'î' => 'i', 'í' => 'i', 'ï' => 'i', 'ĳ' => 'ij',
            'ĵ' => 'j', 'й' => 'j', 'я' => 'ja', 'ю' => 'ju', 'ķ' => 'k', 'к' => 'k',
            'ľ' => 'l', 'ł' => 'l', 'ŀ' => 'l', 'ĺ' => 'l', 'ļ' => 'l', 'л' => 'l',
            'м' => 'm', 'ņ' => 'n', 'ñ' => 'n', 'ń' => 'n', 'н' => 'n', 'ň' => 'n',
            'ŉ' => 'n', 'ó' => 'o', 'ò' => 'o', 'ǒ' => 'o', 'ő' => 'o', 'о' => 'o',
            'ō' => 'o', 'º' => 'o', 'ơ' => 'o', 'ŏ' => 'o', 'ô' => 'o', 'ö' => 'oe',
            'õ' => 'o', 'ø' => 'o', 'ǿ' => 'o', 'œ' => 'oe', 'п' => 'p', 'р' => 'r',
            'ř' => 'r', 'ŕ' => 'r', 'ŗ' => 'r', 'ſ' => 's', 'ŝ' => 's', 'ș' => 's',
            'š' => 's', 'ś' => 's', 'с' => 's', 'ş' => 's', 'ш' => 'sh', 'щ' => 'shch',
            'ß' => 'ss', 'ţ' => 't', 'т' => 't', 'ŧ' => 't', 'ť' => 't', 'ț' => 't',
            'у' => 'u', 'ǘ' => 'u', 'ŭ' => 'u', 'û' => 'u', 'ú' => 'u', 'ų' => 'u',
            'ù' => 'u', 'ű' => 'u', 'ů' => 'u', 'ư' => 'u', 'ū' => 'u', 'ǚ' => 'u',
            'ǜ' => 'u', 'ǔ' => 'u', 'ǖ' => 'u', 'ũ' => 'u', 'ü' => 'ue', 'в' => 'v',
            'ŵ' => 'w', 'ы' => 'y', 'ÿ' => 'y', 'ý' => 'y', 'ŷ' => 'y', 'ź' => 'z',
            'ž' => 'z', 'з' => 'z', 'ż' => 'z', 'ж' => 'zh', 'ь' => '', 'ъ' => '',
            "\xC2\xA0"     => ' ', "\xE2\x80\x80" => ' ', "\xE2\x80\x81" => ' ',
            "\xE2\x80\x82" => ' ', "\xE2\x80\x83" => ' ', "\xE2\x80\x84" => ' ',
            "\xE2\x80\x85" => ' ', "\xE2\x80\x86" => ' ', "\xE2\x80\x87" => ' ',
            "\xE2\x80\x88" => ' ', "\xE2\x80\x89" => ' ', "\xE2\x80\x8A" => ' ',
            "\xE2\x80\xAF" => ' ', "\xE2\x81\x9F" => ' ', "\xE3\x80\x80" => ' ',
        ];
    }

    /**
     * slugify()
     *
     * Transforms a string into a URL or filesystem-friendly string.
     *
     * Note: Adapted from Illuminate/Support/Str::slug.
     *
     * @see https://packagist.org/packages/laravel/lumen-framework  < v5.5
     * @see http://opensource.org/licenses/MIT
     *
     * @param   string  $title      String to convert.
     * @param   string  $separator  Separator used to separate words in $title.
     * @return  string
     */
    public static function slugify(string $title, string $separator = '-'): string
    {
        $title = Strings::ascii($title);

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
     * randomBytes()
     *
     * Generate cryptographically secure pseudo-random bytes.
     *
     * @param   int<1, max>  $length  Length of the random string that should be returned in bytes.
     * @return  string
     *
     * @throws RandomException
     * @throws ValueError
     */
    public static function randomBytes(int $length): string
    {
        // Sanity check
        if ($length < 1) { // @phpstan-ignore-line
            throw new RandomException('$length must be greater than 1.');
        }

        // Generate bytes
        return random_bytes($length);
    }

    /**
     * randomString()
     *
     * Generates a secure random string, based on {@see static::randomBytes()}.
     *
     * @param   int<min, max>  $length  Length the random string should be.
     * @return  string
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
     * validEmail()
     *
     * Validate an email address using PHP's built-in filter.
     *
     * @param   string  $email Value to check.
     * @return  bool
     */
    public static function validEmail(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * validJson()
     *
     * Determines if a string is valid JSON.
     *
     * @deprecated as of 2.0.0
     *
     * @param   string  $data   The string to validate as JSON.
     * @return  bool
     */
    public static function validJson(string $data): bool
    {
        $data = trim($data);
        json_decode($data);

        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * obscureEmail()
     *
     * Obscures an email address.
     *
     * @param   string  $email  Email address to obscure.
     * @return  string          Obscured email address.
     *
     * @throws  InvalidArgumentException
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
            /** @scrutinizer ignore-type */ str_split($email)
        );

        return implode('', $email);
    }

    /**
     * guid()
     *
     * Generate a Globally/Universally Unique Identifier (version 4).
     *
     * @return  string
     *
     * @throws RandomException
     */
    public static function guid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            Numbers::random(0, 0xffff),
            Numbers::random(0, 0xffff),
            Numbers::random(0, 0xffff),
            Numbers::random(0, 0x0fff) | 0x4000,
            Numbers::random(0, 0x3fff) | 0x8000,
            Numbers::random(0, 0xffff),
            Numbers::random(0, 0xffff),
            Numbers::random(0, 0xffff)
        );
    }
}
