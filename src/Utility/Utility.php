<?php

declare(strict_types=1);

/**
 * Utility - Collection of various PHP utility functions.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @package   Utility
 * @link      https://www.secondversion.com/
 * @version   1.3.0
 * @copyright (C) 2017 - 2023 Eric Sizemore
 * @license   The MIT License (MIT)
 */
namespace Esi\Utility;

// Exceptions
use Exception, InvalidArgumentException, RuntimeException, ValueError, TypeError;
use FilesystemIterator, RecursiveDirectoryIterator, RecursiveIteratorIterator;

// Classes
use DateTime, DateTimeZone;

// Functions
use function abs, array_filter, array_keys, array_map, array_pop, array_merge_recursive, array_sum;
use function bin2hex, call_user_func, ceil, chmod, count, date, trigger_error;
use function end, explode, fclose, file, file_get_contents, file_put_contents;
use function filter_var, floatval, fopen, function_exists, hash, header, headers_sent;
use function implode, in_array, inet_ntop, inet_pton, ini_get, ini_set, intval, is_array;
use function is_dir, is_file, is_null, is_readable, is_writable, json_decode, json_last_error;
use function mb_convert_case, mb_stripos, mb_strlen, mb_strpos, mb_substr, natsort;
use function number_format, ord, parse_url, preg_match, preg_quote, preg_replace;
use function random_bytes, random_int, rtrim, sprintf, str_replace, str_split;
use function strcmp, strtr, strval, time, trim, ucwords, unlink, str_contains, str_starts_with, str_ends_with;

// Constants
use const DIRECTORY_SEPARATOR, FILE_IGNORE_NEW_LINES, FILE_SKIP_EMPTY_LINES;
use const FILTER_FLAG_IPV4, FILTER_FLAG_IPV6, FILTER_FLAG_NO_PRIV_RANGE;
use const FILTER_FLAG_NO_RES_RANGE, FILTER_VALIDATE_EMAIL, FILTER_VALIDATE_IP;
use const JSON_ERROR_NONE, MB_CASE_LOWER, MB_CASE_TITLE, MB_CASE_UPPER;
use const PHP_INT_MAX, PHP_INT_MIN, PHP_SAPI, PHP_OS_FAMILY, E_USER_DEPRECATED;

/**
 * Utility - Collection of various PHP utility functions.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @package   Utility
 * @link      https://www.secondversion.com/
 * @version   1.3.0
 * @copyright (C) 2017 - 2023 Eric Sizemore
 * @license   The MIT License (MIT)
 *
 * Copyright (C) 2017 - 2023 Eric Sizemore. All rights reserved.
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
class Utility
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
     * @return  string
     */
    public static function getEncoding(): string
    {
        return self::$encoding;
    }

    /**
     * Sets the encoding to use for multibyte-based functions.
     *
     * @param  string  $newEncoding  Charset
     * @param  bool    $iniUpdate    Update php.ini's default_charset?
     */
    public static function setEncoding(string $newEncoding = '', bool $iniUpdate = false): void
    {
        if ($newEncoding !== '') {
            self::$encoding = $newEncoding;
        }

        if ($iniUpdate) {
            static::iniSet('default_charset', self::$encoding);
            static::iniSet('internal_encoding', self::$encoding);
        }
    }

    /** array related functions **/

    /**
     * arrayFlatten()
     *
     * Flattens a multi-dimensional array.
     *
     * Keys are preserved based on $separator.
     *
     * @param   array<mixed>   $array      Array to flatten.
     * @param   string         $separator  The new keys are a list of original keys separated by $separator.
     *
     * @since 1.2.0
     * @param   string         $prepend    A string to prepend to resulting array keys.
     *
     * @return  array<mixed>               The flattened array.
     */
    public static function arrayFlatten(array $array, string $separator = '.', string $prepend = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && $value !== []) {
                $result = array_merge($result, static::arrayFlatten($value, $separator, $prepend . $key . $separator));
            } else {
                $result[$prepend . $key] = $value;
            }
        }
        return $result;
    }

    /**
     * arrayMapDeep()
     *
     * Recursively applies a callback to all non-iterable elements of an array or an object.
     *
     * @since 1.2.0 - updated with inspiration from the WordPress map_deep() function.
     *      @see https://developer.wordpress.org/reference/functions/map_deep/
     *
     * @param   mixed     $array     The array to apply $callback to.
     * @param   callable  $callback  The callback function to apply.
     * @return  mixed
     */
    public static function arrayMapDeep(mixed $array, callable $callback): mixed
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = static::arrayMapDeep($value, $callback);
            }
        } elseif (is_object($array)) {
            foreach (get_object_vars($array) as $key => $value) {
                $array->$key = static::arrayMapDeep($value, $callback);
            }
        } else {
            $array = call_user_func($callback, $array);
        }
        return $array;
    }

    /**
     * arrayInterlace()
     * 
     * Interlaces one or more arrays' values (not preserving keys).
     *
     * Example:
     *
     *      var_dump(Utility::arrayInterlace(
     *          [1, 2, 3],
     *          ['a', 'b', 'c']
     *      ));
     *
     * Result:
     *      Array (
     *          [0] => 1
     *          [1] => a
     *          [2] => 2
     *          [3] => b
     *          [4] => 3
     *          [5] => c
     *      )
     *
     * @since 1.2.0
     *
     * @param  array<mixed>        ...$args
     * @return array<mixed>|false
     */
    public static function arrayInterlace(array ...$args): array | false
    {
        $numArgs = count($args);

        if ($numArgs === 0) {
            return false;
        }

        if ($numArgs === 1) {
            return $args[0];
        }

        $newArray = [];
        $totalElements = array_sum(array_map('count', $args));

        for ($i = 0; $i < $totalElements; $i++) {
            foreach ($args as $arr) {
                if (isset($arr[$i])) {
                    $newArray[] = $arr[$i];
                }
            }
        }
        return $newArray;
    }

    /** string related functions **/

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
        return strcmp(static::upper($str1), static::upper($str2));
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
            $haystack = static::lower($haystack);
            $needle   = static::lower($needle);
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
            $haystack = static::lower($haystack);
            $needle   = static::lower($needle);
        }
        return (
            $multibyte
            ? static::substr($haystack, -static::length($needle)) === $needle
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
            $haystack = static::lower($haystack);
            $needle   = static::lower($needle);
        }
        return (
            $multibyte
            ? mb_strpos($haystack, $needle) !== false
            : str_contains($haystack, $needle) !== false
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
        if ($insensitive) {
            $haystack = static::lower($haystack);
            $needle   = static::lower($needle);
        }
        return (
            $multibyte
            ? mb_strpos($haystack, $needle) === false
            : str_contains($haystack, $needle) === false
        );
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
     * ascii()
     *
     * Transliterate a UTF-8 value to ASCII.
     *
     * Note: Adapted from Illuminate/Support/Str
     *
     * @see https://packagist.org/packages/laravel/lumen-framework < v5.5
     * @see http://opensource.org/licenses/MIT
     *
     * @param   string  $value  Value to transliterate.
     * @return  string
     */
    public static function ascii(string $value): string
    {
        foreach (static::charMap() as $key => $val) {
            $value = str_replace($key, $val, $value);
        }
        // preg_replace can return null if it encounters an error, so we return
        // the passed $value in that instance.
        return preg_replace('/[^\x20-\x7E]/u', '', $value) ?? $value;
    }

    /**
     * charMap()
     *
     * Returns the replacements for the ascii method.
     *
     * @return  array<string, string>
     */
    protected static function charMap(): array
    {
        static $charMap;

        if (isset($charMap)) {
            return $charMap;
        }
        return $charMap = [
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
            "\xE2\x80\xAF" => ' ', "\xE2\x81\x9F" => ' ', "\xE3\x80\x80" => ' '
        ];
    }

    /**
     * slugify()
     *
     * Transforms a string into a URL or filesystem-friendly string.
     *
     * Note: Adapted from Illuminate/Support/Str::slug
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
        $title = static::ascii($title);

        // preg_replace can return null if an error occurs. It shouldn't happen, but if it does,
        // we return what we have processed thus far
        $title = (
            preg_replace('![' . preg_quote(($separator === '-' ? '_' : '-')) . ']+!u', $separator, $title)
            ?? $title
        );

        // Replace @ with the word 'at'
        $title = str_replace('@', $separator . 'at' . $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = (
            preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', static::lower($title))
            ?? $title
        );

        // Replace all separator characters and whitespace by a single separator
        $title = (
            preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title)
            ?? $title
        );

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
     * @throws \Random\RandomException If the random_bytes() function somehow fails.
     * @throws \ValueError
     */
    public static function randomBytes(int $length): string
    {
        // Generate bytes
        try {
            return random_bytes($length);
        } catch (\Random\RandomException $e) {
            throw new \Random\RandomException(
                'Unable to generate random bytes: ' . $e->getMessage(), $e->getCode(), $e->getPrevious()
            );
        }
    }

    /**
     * randomInt()
     *
     * Generate a cryptographically secure pseudo-random integer.
     *
     * @param   int<min, max>  $min  The lowest value to be returned, which must be PHP_INT_MIN or higher.
     * @param   int<min, max>  $max  The highest value to be returned, which must be less than or equal to PHP_INT_MAX.
     * @return  int<min, max>
     *
     * @throws \Random\RandomException | \ValueError
     */
    public static function randomInt(int $min, int $max): int
    {
        // Generate random int
        try {
            return random_int($min, $max);
        } catch (\Random\RandomException $e) {
            throw new \Random\RandomException(
                'Unable to generate random int: ' . $e->getMessage(), $e->getCode(), $e->getPrevious()
            );
        }
    }

    /**
     * randomString()
     *
     * Generates a secure random string, based on {@see static::randomBytes()}.
     *
     * @todo A better implementation. Could be done better.
     *
     * @param   int<min, max>  $length  Length the random string should be.
     * @return  string
     *
     * @throws \Random\RandomException | \ValueError
     */
    public static function randomString(int $length = 8): string
    {
        // Sanity check
        if ($length <= 0) {
            throw new \Random\RandomException('$length must be greater than 0.');
        }

        // Attempt to get random bytes
        try {
            $bytes = static::randomBytes($length * 2);

            if ($bytes === '') {
                throw new \Random\RandomException('Random bytes generator failure.');
            }
        } catch (\Random\RandomException $e) {
            throw new \Random\RandomException($e->getMessage(), 0, $e);
        }
        // Convert bytes to hexadecimal and truncate to the desired length
        return static::substr(bin2hex($bytes), 0, $length);
    }

    /** directory/file related functions **/

    /**
     * lineCounter()
     *
     * Parse a project directory for approximate line count for a project's
     * codebase.
     *
     * @param   string          $directory      Directory to parse.
     * @param   array<string>   $ignore         Subdirectories of $directory you wish
     *                                          to not include in the line count.
     * @param   array<string>   $extensions     An array of file types/extensions of
     *                                          files you want included in the line count.
     * @param   bool            $skipEmpty      If set to true, will not include empty
     *                                          lines in the line count.
     * @param   bool            $onlyLineCount  If set to true, only returns an array
     *                                          of line counts without directory/filenames.
     * @return  array<mixed>
     *
     * @throws  InvalidArgumentException
     */
    public static function lineCounter(string $directory, array $ignore = [], array $extensions = [], bool $skipEmpty = false, bool $onlyLineCount = false): array
    {
        // Sanity check
        if (!is_dir($directory) || !is_readable($directory)) {
            throw new InvalidArgumentException('Invalid $directory specified');
        }

        // Initialize
        $lines = [];

        // Directory names we wish to ignore
        $ignore = (count($ignore) > 0) ? preg_quote(implode('|', $ignore), '#') : '';

        // Traverse the directory
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $directory,
                FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
            )
        );

        // Build the actual contents of the directory
        /** @var RecursiveDirectoryIterator $val **/
        foreach ($iterator as $key => $val) {
            if (!$val->isFile()) {
                continue;
            }

            if ($ignore !== '' && preg_match("#($ignore)#i", $val->getPath()) === 1) {
                continue;
            }

            if (count($extensions) > 0 && !in_array($val->getExtension(), $extensions, true)) {
                continue;
            }

            $content = file($val->getPath() . DIRECTORY_SEPARATOR . $val->getFilename(), FILE_IGNORE_NEW_LINES | ($skipEmpty ? FILE_SKIP_EMPTY_LINES : 0));

            if ($content === false) {
                continue;
            }
            /** @var int<0, max> $content **/
            $content = count(/** @scrutinizer ignore-type */$content);

            $lines[$val->getPath()][$val->getFilename()] = $content;
        }
        unset($iterator);

        return ($onlyLineCount ? static::arrayFlatten($lines) : $lines);
    }
    /**
     * directorySize()
     *
     * Retrieves size of a directory (in bytes).
     *
     * @param   string          $directory  Directory to parse.
     * @param   array<string>   $ignore     Subdirectories of $directory you wish to not include.
     * @return  int
     *
     * @throws  InvalidArgumentException
     */
    public static function directorySize(string $directory, array $ignore = []): int
    {
        // Sanity checks
        if (!is_dir($directory) || !is_readable($directory)) {
            throw new InvalidArgumentException('Invalid $directory specified');
        }

        // Initialize
        $size = 0;

        // Directories we wish to ignore, if any
        $ignore = (count($ignore) > 0) ? preg_quote(implode('|', $ignore), '#') : '';

        // Traverse the directory
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $directory,
                FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
            )
        );

        // Determine directory size by checking file sizes
        /** @var RecursiveDirectoryIterator $val **/
        foreach ($iterator as $key => $val) {
            if ($ignore !== '' && preg_match("#($ignore)#i", $val->getPath()) === 1) {
                continue;
            }

            if ($val->isFile()) {
                $size += $val->getSize();
            }
        }
        unset($iterator);

        return $size;
    }

    /**
     * Retrieves contents of a directory.
     *
     * @param   string          $directory  Directory to parse.
     * @param   array<string>   $ignore     Subdirectories of $directory you wish to not include.
     * @return  array<mixed>
     *
     * @throws  InvalidArgumentException
     */
    public static function directoryList(string $directory, array $ignore = []): array
    {
        // Sanity checks
        if (!is_dir($directory) || !is_readable($directory)) {
            throw new InvalidArgumentException('Invalid $directory specified');
        }

        // Initialize
        $contents = [];

        // Directories to ignore, if any
        $ignore = (count($ignore) > 0) ? preg_quote(implode('|', $ignore), '#') : '';

        // Traverse the directory
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $directory,
                FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
            )
        );

        // Build the actual contents of the directory
        /** @var RecursiveDirectoryIterator $val **/
        foreach ($iterator AS $key => $val) {
            if ($ignore !== '' && preg_match("#($ignore)#i", $val->getPath()) === 1) {
                continue;
            }
            $contents[] = $key;
        }
        natsort($contents);

        return $contents;
    }

    /**
     * normalizeFilePath()
     *
     * Normalizes a file or directory path.
     *
     * @param   string  $path       The file or directory path.
     * @param   string  $separator  The directory separator. Defaults to DIRECTORY_SEPARATOR.
     * @return  string              The normalized file or directory path
     */
    public static function normalizeFilePath(string $path, string $separator = DIRECTORY_SEPARATOR): string
    {
        // Clean up our path
        $separator = ($separator === '' ? DIRECTORY_SEPARATOR : $separator);

        $path = rtrim(strtr($path, '/\\', $separator . $separator), $separator);

        if (
            static::doesNotContain($separator . $path, "{$separator}.")
            && static::doesNotContain($path, $separator . $separator)
        ) {
            return $path;
        }

        // Initialize
        $parts = [];

        // Grab file path parts
        foreach (explode($separator, $path) as $part) {
            if ($part === '..' && count($parts) > 0 && end($parts) !== '..') {
                array_pop($parts);
            } elseif ($part === '.' || $part === '' && count($parts) > 0) {
                continue;
            } else {
                $parts[] = $part;
            }
        }

        // Build
        $path = implode($separator, $parts);
        return ($path === '' ? '.' : $path);
    }

    /**
     * isReallyWritable()
     *
     * Checks to see if a file or directory is really writable.
     *
     * @param   string  $file  File or directory to check.
     * @return  bool
     *
     * @throws \Random\RandomException  If unable to generate random string for the temp file
     * @throws RuntimeException         If the file or directory does not exist
     */
    public static function isReallyWritable(string $file): bool
    {
        clearstatcache();

        if (!file_exists($file)) {
            throw new RuntimeException('Invalid file or directory specified');
        }

        // If we are on Unix/Linux just run is_writable()
        if (PHP_OS_FAMILY !== 'Windows') {
            return is_writable($file);
        }

        // Otherwise, if on Windows...
        $tmpFile = rtrim($file, '\\/') . DIRECTORY_SEPARATOR . hash('md5', static::randomString()) . '.txt';
        $tmpData = 'tmpData';

        // Attempt to write to the file or directory
        $directoryOrFile = (is_dir($file));
        $data = $directoryOrFile ? file_put_contents($tmpFile, $tmpData, FILE_APPEND) : file_get_contents($file);

        // Clean up temporary file if created
        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }
        return ($data !== false);
    }

    /**
     * fileRead()
     *
     * Perform a read operation on a pre-existing file.
     *
     * @param   string       $file  Filename
     * @return  string|false
     *
     * @throws  InvalidArgumentException
     */
    public static function fileRead(string $file): string | false
    {
        // Sanity check
        if (!is_readable($file)) {
            throw new InvalidArgumentException(sprintf("File '%s' does not exist or is not readable.", $file));
        }
        return file_get_contents($file);
    }

    /**
     * fileWrite()
     *
     * Perform a write operation on a pre-existing file.
     *
     * @param   string  $file   Filename
     * @param   string  $data   If writing to the file, the data to write.
     * @param   int     $flags  Bitwise OR'ed set of flags for file_put_contents. One or
     *                          more of FILE_USE_INCLUDE_PATH, FILE_APPEND, LOCK_EX.
     *                          {@link http://php.net/file_put_contents}
     * @return  string|int<0, max>|false
     *
     * @throws InvalidArgumentException|\Random\RandomException
     */
    public static function fileWrite(string $file, string $data = '', int $flags = 0): string | false | int
    {
        // Sanity checks
        if (!is_readable($file)) {
            throw new InvalidArgumentException(sprintf("File '%s' does not exist or is not readable.", $file));
        }

        if (!static::isReallyWritable($file)) {
            throw new InvalidArgumentException(sprintf("File '%s' is not writable.", $file));
        }

        if ($flags < 0) {
            $flags = 0;
        }
        return file_put_contents($file, $data, $flags);
    }

    /** miscellaneous functions **/

    /**
     * Convert Fahrenheit (Fº) To Celsius (Cº)
     *
     * @since  1.2.0
     *
     * @param  float  $fahrenheit  Value in Fahrenheit
     * @param  bool   $rounded     Whether or not to round the result.
     * @param  int    $precision   Precision to use if $rounded is true.
     * @return float
     */
    public static function fahrenheitToCelsius(float $fahrenheit, bool $rounded = true, int $precision = 2): float
    {
        $result = ($fahrenheit - 32) / 1.8;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Celsius (Cº) To Fahrenheit (Fº)
     *
     * @since  1.2.0
     *
     * @param  float  $celsius    Value in Celsius
     * @param  bool   $rounded    Whether or not to round the result.
     * @param  int    $precision  Precision to use if $rounded is true.
     * @return float
     */
    public static function celsiusToFahrenheit(float $celsius, bool $rounded = true, int $precision = 2): float
    {
        $result = ($celsius * 1.8) + 32;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Celsius (Cº) To Kelvin (K)
     *
     * @since  1.2.0
     *
     * @param  float  $celsius    Value in Celsius
     * @param  bool   $rounded    Whether or not to round the result.
     * @param  int    $precision  Precision to use if $rounded is true.
     * @return float
     */
    public static function celsiusToKelvin(float $celsius, bool $rounded = true, int $precision = 2): float
    {
        $result = $celsius + 273.15;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Kelvin (K) To Celsius (Cº)
     *
     * @since  1.2.0
     *
     * @param  float  $kelvin     Value in Kelvin
     * @param  bool   $rounded    Whether or not to round the result.
     * @param  int    $precision  Precision to use if $rounded is true.
     * @return float
     */
    public static function kelvinToCelsius(float $kelvin, bool $rounded = true, int $precision = 2): float
    {
        $result = $kelvin - 273.15;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Fahrenheit (Fº) To Kelvin (K)
     *
     * @since  1.2.0
     *
     * @param  float  $fahrenheit  Value in Fahrenheit
     * @param  bool   $rounded     Whether or not to round the result.
     * @param  int    $precision   Precision to use if $rounded is true.
     * @return float
     */
    public static function fahrenheitToKelvin(float $fahrenheit, bool $rounded = true, int $precision = 2): float
    {
        $result = (($fahrenheit - 32) / 1.8) + 273.15;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Kelvin (K) To Fahrenheit (Fº)
     *
     * @since  1.2.0
     *
     * @param  float  $kelvin     Value in Kelvin
     * @param  bool   $rounded    Whether or not to round the result.
     * @param  int    $precision  Precision to use if $rounded is true.
     * @return float
     */
    public static function kelvinToFahrenheit(float $kelvin, bool $rounded = true, int $precision = 2): float
    {
        $result = (($kelvin - 273.15) * 1.8) + 32;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Fahrenheit (Fº) To Rankine (ºR)
     *
     * @since  1.2.0
     *
     * @param  float  $fahrenheit  Value in Fahrenheit
     * @param  bool   $rounded     Whether or not to round the result.
     * @param  int    $precision   Precision to use if $rounded is true.
     * @return float
     */
    public static function fahrenheitToRankine(float $fahrenheit, bool $rounded = true, int $precision = 2): float
    {
        $result = $fahrenheit + 459.67;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Rankine (ºR) To Fahrenheit (Fº)
     *
     * @since  1.2.0
     *
     * @param  float  $rankine    Value in Rankine
     * @param  bool   $rounded    Whether or not to round the result.
     * @param  int    $precision  Precision to use if $rounded is true.
     * @return float
     */
    public static function rankineToFahrenheit(float $rankine, bool $rounded = true, int $precision = 2): float
    {
        $result = $rankine - 459.67;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Celsius (Cº) To Rankine (ºR)
     *
     * @since  1.2.0
     *
     * @param  float  $celsius    Value in Celsius
     * @param  bool   $rounded    Whether or not to round the result.
     * @param  int    $precision  Precision to use if $rounded is true.
     * @return float
     */
    public static function celsiusToRankine(float $celsius, bool $rounded = true, int $precision = 2): float
    {
        $result = ($celsius * 1.8) + 491.67;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Rankine (ºR) To Celsius (Cº)
     *
     * @since  1.2.0
     *
     * @param  float  $rankine    Value in Rankine
     * @param  bool   $rounded    Whether or not to round the result.
     * @param  int    $precision  Precision to use if $rounded is true.
     * @return float
     */
    public static function rankineToCelsius(float $rankine, bool $rounded = true, int $precision = 2): float
    {
        $result = ($rankine - 491.67) / 1.8;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Kelvin (K) To Rankine (ºR)
     *
     * @since  1.2.0
     *
     * @param  float  $kelvin     Value in Kelvin
     * @param  bool   $rounded    Whether or not to round the result.
     * @param  int    $precision  Precision to use if $rounded is true.
     * @return float
     */
    public static function kelvinToRankine(float $kelvin, bool $rounded = true, int $precision = 2): float
    {
        $result = (($kelvin - 273.15) * 1.8) + 491.67;

        return ($rounded) ? round($result, $precision) : $result;
    }

    /**
     * Convert Rankine (ºR) To Kelvin (K)
     *
     * @since  1.2.0
     *
     * @param  float  $rankine    Value in Rankine
     * @param  bool   $rounded    Whether or not to round the result.
     * @param  int    $precision  Precision to use if $rounded is true.
     * @return float
     */
    public static function rankineToKelvin(float $rankine, bool $rounded = true, int $precision = 2): float
    {
        $result = (($rankine - 491.67) / 1.8) + 273.15;

        return ($rounded) ? round($result, $precision) : $result;
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
     * @param   string  $data   The string to validate as JSON.
     * @return  bool
     */
    public static function validJson(string $data): bool
    {
        $data = trim($data);

        // PHP >= 8.3?
        if (function_exists('\\json_validate')) {
            return \json_validate($data);
        }

        json_decode($data);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * sizeFormat()
     *
     * Format bytes to a human-readable format.
     *
     * @param   int     $bytes     The number in bytes.
     * @param   int     $decimals  How many decimal points to include.
     * @return  string
     */
    public static function sizeFormat(int $bytes, int $decimals = 0): string
    {
        static $pow;

        //
        if (is_null($pow)) {
            $pow = [
                'kilo'  => 1024,
                'mega'  => 1024 ** 2,
                'giga'  => 1024 ** 3,
                'tera'  => 1024 ** 4,
                'peta'  => 1024 ** 5,
                'exa'   => 1024 ** 6,
                'zeta'  => 1024 ** 7,
                'yotta' => 1024 ** 8
            ];
        }

        //
        $bytes = floatval($bytes);

        return match (true) {
            $bytes >= $pow['yotta'] => number_format(($bytes / $pow['yotta']), $decimals, '.', '') . ' YiB',
            $bytes >= $pow['zeta']  => number_format(($bytes / $pow['zeta']), $decimals, '.', '') . ' ZiB',
            $bytes >= $pow['exa']   => number_format(($bytes / $pow['exa']), $decimals, '.', '') . ' EiB',
            $bytes >= $pow['peta']  => number_format(($bytes / $pow['peta']), $decimals, '.', '') . ' PiB',
            $bytes >= $pow['tera']  => number_format(($bytes / $pow['tera']), $decimals, '.', '') . ' TiB',
            $bytes >= $pow['giga']  => number_format(($bytes / $pow['giga']), $decimals, '.', '') . ' GiB',
            $bytes >= $pow['mega']  => number_format(($bytes / $pow['mega']), $decimals, '.', '') . ' MiB',
            $bytes >= $pow['kilo']  => number_format(($bytes / $pow['kilo']), $decimals, '.', '') . ' KiB',
            default => number_format($bytes, $decimals, '.', '') . ' B'
        };
    }

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
     * @return  string
     *
     * @throws  InvalidArgumentException|Exception
     */
    public static function timeDifference(int $timestampFrom, int $timestampTo = 0, string $timezone = 'UTC', string $append = ' old'): string
    {
        static $validTimezones;

        if (!$validTimezones) {
            $validTimezones = DateTimeZone::listIdentifiers();
        }

        // Check to see if it is a valid timezone
        $timezone = $timezone ?: 'UTC';

        if (!in_array($timezone, $validTimezones, true)) {
            throw new InvalidArgumentException('$timezone appears to be invalid.');
        }

        // Normalize timestamps
        $timestampTo = ($timestampTo <= 0) ? time() : $timestampTo;

        if ($timestampFrom <= 0) {
            throw new InvalidArgumentException('$timestampFrom must be greater than 0.');
        }

        // Create DateTime objects and set timezone
        $timestampFrom = (new DateTime('@' . $timestampFrom))->setTimezone(new DateTimeZone($timezone));
        $timestampTo = (new DateTime('@' . $timestampTo))->setTimezone(new DateTimeZone($timezone));

        // Calculate difference
        $difference = $timestampFrom->diff($timestampTo);

        // Format the difference
        $string = match (true) {
            $difference->y > 0 => $difference->y . ' year(s)',
            $difference->m > 0 => $difference->m . ' month(s)',
            $difference->d >= 7 => ceil($difference->d / 7) . ' week(s)',
            $difference->d > 0 => $difference->d . ' day(s)',
            $difference->h > 0 => $difference->h . ' hour(s)',
            $difference->i > 0 => $difference->i . ' minute(s)',
            $difference->s > 0 => $difference->s . ' second(s)',
            default => ''
        };
        return $string . $append;
    }

    /**
     * getIpAddress()
     *
     * Return the visitor's IP address.
     *
     * @param   bool    $trustProxy  Whether to trust HTTP_CLIENT_IP and
     *                               HTTP_X_FORWARDED_FOR.
     * @return  string
     */
    public static function getIpAddress(bool $trustProxy = false): string
    {
        // Pretty self-explanatory. Try to get an 'accurate' IP
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        if (!$trustProxy) {
            /** @var string **/
            return $_SERVER['REMOTE_ADDR'];
        }

        $ip = '';
        $ips = [];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            /** @var string $ips **/
            $ips = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $ips = explode(',', $ips);
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
            /** @var string $ips **/
            $ips = $_SERVER['HTTP_X_REAL_IP'];
            $ips = explode(',', $ips);
        }

        /** @var  array<mixed> $ips **/
        $ips = static::arrayMapDeep($ips, 'trim');

        if (count($ips) > 0) {
            foreach ($ips as $val) {
                /** @phpstan-ignore-next-line */
                if (inet_ntop(inet_pton($val)) === $val && static::isPublicIp($val)) {
                    /** @var string $ip **/
                    $ip = $val;
                    break;
                }
            }
        }
        unset($ips);

        if ($ip === '') {
            /** @var string $ip **/
            $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * isPrivateIp()
     *
     * Determines if an IP address is within the private range.
     *
     * @param   string  $ipaddress  IP address to check.
     * @return  bool
     */
    public static function isPrivateIp(string $ipaddress): bool
    {
        return !(bool) filter_var(
            $ipaddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE
        );
    }

    /**
     * isReservedIp()
     *
     * Determines if an IP address is within the reserved range.
     *
     * @param   string  $ipaddress  IP address to check.
     * @return  bool
     */
    public static function isReservedIp(string $ipaddress): bool
    {
        return !(bool) filter_var(
            $ipaddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * isPublicIp()
     *
     * Determines if an IP address is not within the private or reserved ranges.
     *
     * @param   string  $ipaddress  IP address to check.
     * @return  bool
     */
    public static function isPublicIp(string $ipaddress): bool
    {
        return (!static::isPrivateIp($ipaddress) && !static::isReservedIp($ipaddress));
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
        if (!static::validEmail($email)) {
            throw new InvalidArgumentException('Invalid $email specified.');
        }

        // Split and process
        $email = array_map(function($char) {
            return '&#' . ord($char) . ';';
        }, /** @scrutinizer ignore-type */ str_split($email));

        return implode('', $email);
    }

    /**
     * currentHost()
     *
     * Determines current hostname.
     *
     * @param   bool    $stripWww         True to strip www. off the host, false to leave it be.
     * @param   bool    $acceptForwarded  True to accept 
     * @return  string
     */
    public static function currentHost(bool $stripWww = false, bool $acceptForwarded = false): string
    {
        /** @var string $host **/
        $host = (
            ($acceptForwarded && isset($_SERVER['HTTP_X_FORWARDED_HOST'])) ? 
            $_SERVER['HTTP_X_FORWARDED_HOST'] : 
            ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '')
        );
        $host = trim(strval($host));

        if ($host === '' || preg_match('#^\[?(?:[a-z0-9-:\]_]+\.?)+$#', $host) === 0) {
            $host = 'localhost';
        }

        $host = static::lower($host);

        // Strip 'www.'
        if ($stripWww) {
            $strippedHost = preg_replace('#^www\.#', '', $host);
        }
        return ($strippedHost ?? $host);
    }

    /**
     * serverHttpVars()
     *
     * Builds an array of headers based on HTTP_* keys within $_SERVER.
     *
     * @deprecated Since 2.0.0, now defaults to getallheaders()
     *
     * @param   bool  $asLowerCase
     * @return  array<mixed>
     */
    public static function serverHttpVars(bool $asLowerCase = false): array
    {
        return \getallheaders();
    }

    /**
     * isHttps()
     *
     * Checks to see if SSL is in use.
     *
     * @return  bool
     */
    public static function isHttps(): bool
    {
        $headers = \getallheaders();

        // Check if any of the HTTPS indicators are present
        return (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off') ||
            (isset($headers['X-Forwarded-Proto']) && $headers['X-Forwarded-Proto'] === 'https') ||
            (isset($headers['Front-End-Https']) && $headers['Front-End-Https'] !== 'off')
        );
    }

    /**
     * currentUrl()
     *
     * Retrieve the current URL.
     *
     * @return  string
     */
    public static function currentUrl(): string
    {
        // Scheme
        $scheme = (static::isHttps()) ? 'https://' : 'http://';

        // Auth
        $auth = '';

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $auth = $_SERVER['PHP_AUTH_USER'] . (
                isset($_SERVER['PHP_AUTH_PW']) ? ':' . $_SERVER['PHP_AUTH_PW'] : ''
            ) . '@';
        }

        // Host and port
        $host = static::currentHost();

        /** @var int $port **/
        $port = $_SERVER['SERVER_PORT'] ?? 0;
        $port = ($port === (static::isHttps() ? 443 : 80) || $port === 0) ? '' : ":$port";

        // Path
        /** @var string $self **/
        $self = $_SERVER['PHP_SELF'];
        /** @var string $query **/
        $query = $_SERVER['QUERY_STRING'] ?? '';
        /** @var string $request **/
        $request = $_SERVER['REQUEST_URI'] ?? '';
        /** @var string $path **/
        $path = ($request === '' ? $self . ($query !== '' ? '?' . $query : '') : $request);

        // Put it all together
        /** @var string $url **/
        $url = sprintf('%s%s%s%s%s', $scheme, $auth, $host, $port, $path);

        return $url;
    }

    /**
     * ordinal()
     *
     * Retrieve the ordinal version of a number.
     *
     * Basically, it will append th, st, nd, or rd based on what the number ends with.
     *
     * @param   int     $number  The number to create an ordinal version of.
     * @return  string
     */
    public static function ordinal(int $number): string
    {
        static $suffixes = ['th', 'st', 'nd', 'rd'];

        $absNumber = abs($number);

        if ($absNumber % 100 >= 11 && $absNumber % 100 <= 13) {
            $suffix = $suffixes[0];
        } else {
            $suffix = $suffixes[$absNumber % 10] ?? $suffixes[0];
        }
        return $number . $suffix;
    }

    /**
     * guid()
     *
     * Generate a Globally/Universally Unique Identifier (version 4).
     *
     * @return  string
     * @throws \Random\RandomException
     */
    public static function guid(): string
    {
        static $format = '%04x%04x-%04x-%04x-%04x-%04x%04x%04x';

        try {
            $guid = sprintf(
                $format,
                static::randomInt(0, 0xffff),
                static::randomInt(0, 0xffff),
                static::randomInt(0, 0xffff),
                static::randomInt(0, 0x0fff) | 0x4000,
                static::randomInt(0, 0x3fff) | 0x8000,
                static::randomInt(0, 0xffff),
                static::randomInt(0, 0xffff),
                static::randomInt(0, 0xffff)
            );
        } catch (\Random\RandomException $e) {
            throw new \Random\RandomException('Unable to generate GUID: ' . $e->getMessage(), 0, $e);
        }
        return $guid;
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
     * @return  array<mixed>
     *
     * @throws  InvalidArgumentException|Exception
     */
    public static function timezoneInfo(string $timezone): array
    {
        static $validTimezones;

        if (!$validTimezones) {
            $validTimezones = DateTimeZone::listIdentifiers();
        }

        // Check if it is a valid timezone
        $timezone = $timezone ?: 'UTC';

        if (!in_array($timezone, $validTimezones, true)) {
            throw new InvalidArgumentException('$timezone appears to be invalid.');
        }

        try {
            $tz = new DateTimeZone($timezone);
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage(), 0, $e);
        }

        $location = $tz->getLocation();

        $info = [
            'offset'    => $tz->getOffset(new DateTime('now', new DateTimeZone('GMT'))) / 3600,
            'country'   => $location['country_code'] ?? 'N/A',
            'latitude'  => $location['latitude'] ?? 'N/A',
            'longitude' => $location['longitude'] ?? 'N/A',
            'dst'       => $tz->getTransitions(time(), time())[0]['isdst'] ?? null,
        ];
        return $info;
    }

    /**
     * iniGet()
     *
     * Safe ini_get taking into account its availability.
     *
     * @param   string  $option       The configuration option name.
     * @param   bool    $standardize  Standardize returned values to 1 or 0?
     * @return  string|false
     *
     * @throws  RuntimeException|InvalidArgumentException
     */
    public static function iniGet(string $option, bool $standardize = false): string | false
    {
        if (!function_exists('ini_get')) {
            // disabled_functions?
            throw new RuntimeException('Native ini_get function not available.');
        }

        if ($option === '') {
            throw new InvalidArgumentException('$option must not be empty.');
        }

        $value = ini_get($option);

        if ($value === false) {
            throw new RuntimeException('$option does not exist.');
        }

        $value = trim($value);

        if ($standardize) {
            $value = match (static::lower($value)) {
                'yes', 'on', 'true', '1' => '1',
                'no', 'off', 'false', '0' => '0',
                default => $value
            };
        }
        return $value;
    }

    /**
     * iniSet()
     *
     * Safe ini_set taking into account its availability.
     *
     * @param   string  $option  The configuration option name.
     * @param   string  $value   The new value for the option.
     * @return  string|false
     *
     * @throws RuntimeException|InvalidArgumentException
     */
    public static function iniSet(string $option, string $value): string | false
    {
        if (!function_exists('ini_set')) {
            // disabled_functions?
            throw new RuntimeException('Native ini_set function not available.');
        }

        if ($option === '') {
            throw new InvalidArgumentException('$option must not be empty.');
        }
        return ini_set($option, $value);
    }
}
