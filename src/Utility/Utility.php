<?php

/**
 * Utility - Collection of various PHP utility functions.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @package   Utility
 * @link      http://www.secondversion.com/
 * @version   1.0.1
 * @copyright (C) 2017 - 2019 Eric Sizemore
 * @license   The MIT License (MIT)
 */
namespace Esi\Utility;

/**
 * Utility - Collection of various PHP utility functions.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @package   Utility
 * @link      http://www.secondversion.com/
 * @version   1.0.1
 * @copyright (C) 2017 - 2019 Eric Sizemore
 * @license   The MIT License (MIT)
 *
 * Copyright (C) 2017 - 2019 Eric Sizemore. All rights reserved.
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
    private static $encoding = 'UTF-8';

    /**
     * Returns current encoding.
     *
     * @return  string
     */
    public static function getEncoding(): string
    {
        return static::$encoding;
    }

    /**
     * Sets the encoding to use for multibyte-based functions.
     *
     * @param  string  $newEncoding  Charset
     * @param  bool    $iniUpdate    Update php.ini's default_charset?
     */
    public static function setEncoding(string $newEncoding = '', bool $iniUpdate = false)
    {
        if (!empty($newEncoding)){
            static::$encoding = $newEncoding;
        }

        if ($iniUpdate) {
            static::iniSet('default_charset', static::$encoding);
            static::iniSet('internal_encoding', static::$encoding);
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
     * @param   array   $array      Array to flatten.
     * @param   string  $separator  The new keys are a list of original keys 
     *                              separated by $separator.
     * @return  array               The flattened array.
     */
    public static function arrayFlatten(array $array, string $separator = '.'): array
    {
        $result  = [];
        $stack[] = ['', $array];

        while (\count($stack) > 0) {
            list($prefix, $array) = \array_pop($stack);

            foreach ($array AS $key => $value) {
                $_key = $prefix . \strval($key);

                if (\is_array($value)) {
                    $stack[] = [$_key . $separator, $value];
                } else {
                    $result[$_key] = $value;
                }
            }
        }
        return $result;
    }

    /**
     * arrayMapDeep()
     *
     * Recursively applies a callback to all elements of the given array.
     *
     * @param   array   $array     The array to apply $callback to.
     * @param   string  $callback  The callback function to apply.
     * @return  array
     */
    public static function arrayMapDeep(array $array, callable $callback): array
    {
        foreach ($array AS $key => $value) {
            if (\is_array($array[$key])) {
                $array[$key] = static::arrayMapDeep($array[$key], $callback);
            } else {
                $array[$key] = \call_user_func($callback, $array[$key]);
            }
        }
        return $array;
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
        return \mb_convert_case($value, \MB_CASE_TITLE);
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
        return \mb_convert_case($value, \MB_CASE_LOWER);
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
        return \mb_convert_case($value, \MB_CASE_UPPER);
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
    public static function substr(string $string, int $start, $length = null): string
    {
        return \mb_substr($string, $start, $length);
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
     * @return  bool
     */
    public static function beginsWith(string $haystack, string $needle, bool $insensitive = false): bool
    {
        if ($insensitive) {
            return \mb_stripos($haystack, $needle) === 0;
        }
        return \mb_strpos($haystack, $needle) === 0;
    }

    /**
     * endsWith()
     *
     * Determine if a string ends with another string.
     *
     * @param   string  $haystack     String to search in.
     * @param   string  $needle       String to check for.
     * @param   bool    $insensitive  True to do a case-insensitive search.
     * @return  bool
     */
    public static function endsWith(string $haystack, string $needle, bool $insensitive = false): bool
    {
        if ($insensitive) {
            $haystack = static::lower($haystack);
            $needle   = static::upper($needle);
        }
        return static::substr($haystack, -static::length($needle)) === $needle;
    }

    /**
     * doesContain()
     *
     * Determine if a string exists within another string.
     *
     * @param   string  $haystack     String to search in.
     * @param   string  $needle       String to check for.
     * @param   bool    $insensitive  True to do a case-insensitive search.
     * @return  bool
     */
    public static function doesContain(string $haystack, string $needle, bool $insensitive = false): bool
    {
        if ($insensitive) {
            return \mb_stripos($haystack, $needle) !== false;
        }
        return \mb_strpos($haystack, $needle) !== false;
    }

    /**
     * doesNotContain()
     *
     * Determine if a string does not exist within another string.
     *
     * @param   string  $haystack     String to search in.
     * @param   string  $needle       String to check for.
     * @param   bool    $insensitive  True to do a case-insensitive search.
     * @return  bool
     */
    public static function doesNotContain(string $haystack, string $needle, bool $insensitive = false): bool
    {
        if ($insensitive) {
            return \mb_stripos($haystack, $needle) === false;
        }
        return \mb_strpos($haystack, $needle) === false;
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
        if ($binarySafe) {
            return \mb_strlen($string, '8bit');
        }
        return \mb_strlen($string);
    }

    /**
     * ascii()
     *
     * Transliterate a UTF-8 value to ASCII.
     *
     * Note: Adapted from Illuminate/Support/Str
     *
     * @see https://packagist.org/packages/laravel/lumen-framework
     * @see http://opensource.org/licenses/MIT
     *
     * @param   string  $value  Value to transliterate.
     * @return  string
     */
    public static function ascii(string $value): string
    {
        foreach (static::charMap() AS $key => $val) {
            $value = \str_replace($key, $val, $value);
        }
        return \preg_replace('/[^\x20-\x7E]/u', '', $value);
    }

    /**
     * charMap()
     *
     * Returns the replacements for the ascii method.
     *
     * @return  array
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
     * @see https://packagist.org/packages/laravel/lumen-framework
     * @see http://opensource.org/licenses/MIT
     * 
     * @param   string  $title      String to convert.
     * @param   string  $separator  Separator used to separate words in $title.
     * @return  string
     */
    public static function slugify(string $title, string $separator = '-'): string
    {
        $title = static::ascii($title);
        $title = \preg_replace('![' . \preg_quote(($separator == '-' ? '_' : '-')) . ']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = \preg_replace('![^' . \preg_quote($separator) . '\pL\pN\s]+!u', '', static::lower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = \preg_replace('![' . \preg_quote($separator) . '\s]+!u', $separator, $title);

        // Cleanup $title
        $title = \trim($title, $separator);

        return $title;
    }

    /**
     * randomBytes()
     *
     * Generate cryptographically secure pseudo-random bytes.
     *
     * @param   int     $length  Length of the random string that should be returned in bytes.
     * @return  string
     *
     * @throws LengthException If an invalid length is specified.
     * @throws Exception If the random_bytes() function somehow fails.
     */
    public static function randomBytes(int $length): string
    {
        // Sanity check
        if ($length < 1 OR $length > \PHP_INT_MAX) {
            throw new \LengthException('Invalid $length specified.');
        }

        // Generate bytes
        $error = '';

        // Looks weird, but random_bytes can throw 3 different types of errors.
        try {
            $bytes = \random_bytes($length);
        } catch (\TypeError $e) {
            $error = $e->getMessage();
        } catch (\Error $e) {
            $error = $e->getMessage();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (\trim($error) != '') {
            throw new \Exception($error);
        }
        return $bytes;
    }

    /**
     * randomInt()
     *
     * Generate a cryptographically secure pseudo-random integer.
     * 
     * @param   int  $min  The lowest value to be returned, which must be PHP_INT_MIN or higher.
     * @param   int  $max  The highest value to be returned, which must be less than or equal to PHP_INT_MAX.
     * @return  int
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function randomInt(int $min, int $max): int
    {
        // Sanity check
        if ($min < \PHP_INT_MIN) {
            throw new \InvalidArgumentException('$min value too low.');
        }

        if ($max > \PHP_INT_MAX) {
            throw new \InvalidArgumentException('$max value too high.');
        }

        if ($min >= $max) {
            throw new \InvalidArgumentException('$min value must be less than $max.');
        }

        // Generate
        $error = '';

        // Looks weird, but random_int can throw 3 different types of errors.
        try {
            $int = \random_int($min, $max);
        } catch (\TypeError $e) {
            $error = $e->getMessage();
        } catch (\Error $e) {
            $error = $e->getMessage();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (\trim($error) != '') {
            throw new \Exception($error);
        }
        return $int;
    }

    /**
     * randomString()
     *
     * Generates a secure random string, based on {@see static::randomBytes()}.
     *
     * @todo A better implementation. Could be done better.
     *
     * @param   int     $length  Length the random string should be.
     * @return  string
     *
     * @throws LengthException
     * @throws Exception
     */
    public static function randomString(int $length = 8): string
    {
        // Sanity check
        if ($length <= 0) {
            throw new \LengthException('$length must be greater than 0.');
        }

        //
        $bytes = false;

        try {
            $bytes = static::randomBytes($length * 2);

            if ($bytes === false) {
                throw new \Exception('Random bytes generator failure.');
            }
        }
        catch (Exception $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }

        $bytes = static::substr(\bin2hex($bytes), 0, $length);

        return $bytes;
    }

    /** directory/file related functions **/

    /**
     * lineCounter()
     *
     * Parse a project directory for approximate line count for a project's 
     * codebase.
     *
     * @param   string  $directory      Directory to parse.
     * @param   array   $ignore         Sub-directories of $directory you wish 
     *                                  to not include in the line count.
     * @param   array   $extensions     An array of file types/extensions of 
     *                                  files you want included in the line count.
     * @param   bool    $skipEmpty      If set to true, will not include empty 
     *                                  lines in the line count.
     * @param   bool    $onlyLineCount  If set to true, only returns an array 
     *                                  of line counts without directory/filenames.
     * @return  array
     */
    public static function lineCounter(
        string $directory, 
        array $ignore = [], 
        array $extensions = [], 
        bool $skipEmpty = true, 
        bool $onlyLineCount = false
    ): array
    {
        // Sanity check
        if (empty($directory) OR !\is_dir($directory)) {
            throw new \InvalidArgumentException('Invalid $directory provided.');
        }

        if (!\is_readable($directory)) {
            throw new \InvalidArgumentException('Unable to read $directory.');
        }

        //
        $lines = [];

        //
        $flags = \FILE_IGNORE_NEW_LINES;

        if ($skipEmpty) {
            $flags |= \FILE_SKIP_EMPTY_LINES;
        }

        //
        if (!empty($ignore)) {
            $ignore = \preg_quote(\implode('|', $ignore), '#');
        }

        //
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $directory, 
                \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS
            )
        );

        foreach ($iterator AS $key => $val) {
            if ($val->isFile()) {
                if (
                    (!empty($ignore) AND \preg_match("#($ignore)#i", $val->getPath()))
                    OR (!empty($extensions) AND !\in_array($val->getExtension(), $extensions))
                ) {
                    continue;
                }

                $content = \file($val->getPath() . \DIRECTORY_SEPARATOR . $val->getFilename(), $flags);
                $content = \count($content);

                $lines[$val->getPath()][$val->getFilename()] = $content;
            }
        }
        unset($iterator);

        //
        if ($onlyLineCount) {
            return static::arrayFlatten($lines);
        }
        return $lines;
    }

    /**
     * directorySize()
     *
     * Retrieves size of a directory (in bytes).
     *
     * @param   string  $directory  Directory to parse.
     * @param   array   $ignore     Sub-directories of $directory you wish to not
     *                              include.
     * @return  int
     */
    public static function directorySize(string $directory, array $ignore = []): int
    {
        // Sanity checks
        if (empty($directory) OR !\is_dir($directory)) {
            throw new \InvalidArgumentException('Invalid $directory provided.');
        }

        if (!\is_readable($directory)) {
            throw new \InvalidArgumentException('Unable to read $directory.');
        }

        //
        $size = 0;

        //
        if (!empty($ignore)) {
            $ignore = \preg_quote(\implode('|', $ignore), '#');
        }

        //
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $directory, 
                \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS
            )
        );

        foreach ($iterator AS $key => $val) {
            if (!empty($ignore) AND \preg_match("#($ignore)#i", $val->getPath())) {
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
     * @param   string  $directory  Directory to parse.
     * @param   array   $ignore     Sub-directories of $directory you wish to not
     *                              include.
     * @return  array
     */
    public static function directoryList(string $directory, array $ignore = []): array
    {
        // Sanity checka
        if (empty($directory) OR !\is_dir($directory)) {
            throw new \InvalidArgumentException('Invalid $directory provided.');
        }

        if (!\is_readable($directory)) {
            throw new \InvalidArgumentException('Unable to read $directory.');
        }

        //
        $contents = [];

        //
        if (!empty($ignore)) {
            $ignore = \preg_quote(\implode('|', $ignore), '#');
        }

        //
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $directory, 
                \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS
            )
        );

        foreach ($iterator AS $key => $val) {
            if (!empty($ignore) AND \preg_match("#($ignore)#i", $val->getPath())) {
                continue;
            }
            $contents[] = $key;
        }
        \natsort($contents);

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
    public static function normalizeFilePath(string $path, string $separator = \DIRECTORY_SEPARATOR): string
    {
        //
        $path = \rtrim(\strtr($path, '/\\', $separator . $separator), $separator);

        if (
            static::doesNotContain($separator . $path, "{$separator}.") 
            AND static::doesNotContain($path, $separator . $separator)
        ) {
            return $path;
        }

        //
        $parts = [];

        foreach (\explode($separator, $path) AS $part) {
            if ($part === '..' AND !empty($parts) AND \end($parts) !== '..') {
                \array_pop($parts);
            } elseif ($part === '.' OR $part === '' AND !empty($parts)) {
                continue;
            } else {
                $parts[] = $part;
            }
        }

        //
        $path = \implode($separator, $parts);
        $path = ($path) ?: '.';

        return $path;
    }

    /**
     * isReallyWritable()
     *
     * Checks to see if a file or directory is really writable.
     *
     * @param   string  $file  File or directory to check.
     * @return  bool
     */
    public static function isReallyWritable(string $file): bool
    {
        // If we are on Unix/Linux just run is_writable()
        if (\DIRECTORY_SEPARATOR === '/') {
            return \is_writable($file);
        }

        // Otherwise, if on Windows...
        if (\is_dir($file)) {
            // Generate random filename.
            $file = \rtrim($file, '\\/') . \DIRECTORY_SEPARATOR;
            $file .= \hash('md5', static::getRandomString());

            if (!($fp = \fopen($file, 'ab'))) {
                return false;
            }
            \fclose($fp);
            \chmod($file, 0777);
            @\unlink($file);
        } else {
            if (!\is_file($file) OR !($fp = \fopen($file, 'ab'))) {
                return false;
            }
            \fclose($fp);
        }
        return true;
    }

    /**
     * fileRead()
     *
     * Perform a read operation on a pre-existing file.
     *
     * @param   string  $file  Filename
     * @return  mixed
     */
    public static function fileRead(string $file)
    {
        // Sanity check
        if (!\is_readable($file)) {
            throw new \InvalidArgumentException(\sprintf("File '%s' does not exist or is not readable.", $file));
        }
        return $contents = \file_get_contents($file);
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
     * @return  mixed
     */
    public static function fileWrite(string $file, string $data = '', int $flags = 0)
    {
        // Sanity checks
        if (!\is_readable($file)) {
            throw new \InvalidArgumentException(\sprintf("File '%s' does not exist or is not readable.", $file));
        }

        if (!static::isReallyWritable($file)) {
            throw new \InvalidArgumentException(\sprintf("File '%s' is not writable.", $file));
        }

        if ($flags < 0) {
            $flags = 0;
        }
        return $written = \file_put_contents($file, $data, $flags);
    }

    /** miscellaneous functions **/

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
        return (bool)\filter_var($email, \FILTER_VALIDATE_EMAIL);
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
        if (empty($data)) {
            return false;
        }

        if (($validate = \json_decode($data, true)) === NULL) {
            return false;
        }

        if (\json_last_error() !== \JSON_ERROR_NONE) {
            return false;
        }
        return true;
    }

    /**
     * sizeFormat()
     *
     * Format bytes to a human readable format.
     *
     * @param   int     $bytes     The number in bytes.
     * @param   int     $decimals  How many decimal points to include.
     * @return  string
     */
    public static function sizeFormat(int $bytes, int $decimals = 0): string
    {
        static $pow;

        //
        if (\is_null($pow)) {
            $pow = [
                'kilo'  => 1024,
                'mega'  => \pow(1024, 2),
                'giga'  => \pow(1024, 3),
                'tera'  => \pow(1024, 4),
                'peta'  => \pow(1024, 5),
                'exa'   => \pow(1024, 6),
                'zeta'  => \pow(1024, 7),
                'yotta' => \pow(1024, 8)
            ];
        }

        //
        $bytes = \floatval($bytes);

        /**
         * @todo  Cleanup just a little bit.
         */
        switch (true) {
            case ($bytes >= $pow['yotta']):
                $bytes = \number_format(($bytes / $pow['yotta']), $decimals, '.', '');
                $bytes .= ' YiB';
                break;
            case ($bytes >= $pow['zeta']):
                $bytes = \number_format(($bytes / $pow['zeta']), $decimals, '.', '');
                $bytes .= ' ZiB';
                break;
            case ($bytes >= $pow['exa']):
                $bytes = \number_format(($bytes / $pow['exa']), $decimals, '.', '');
                $bytes .= ' EiB';
                break;
            case ($bytes >= $pow['peta']):
                $bytes = \number_format(($bytes / $pow['peta']), $decimals, '.', '');
                $bytes .= ' PiB';
                break;
            case ($bytes >= $pow['tera']):
                $bytes = \number_format(($bytes / $pow['tera']), $decimals, '.', '');
                $bytes .= ' TiB';
                break;
            case ($bytes >= $pow['giga']):
                $bytes = \number_format(($bytes / $pow['giga']), $decimals, '.', '');
                $bytes .= ' GiB';
                break;
            case ($bytes >= $pow['mega']):
                $bytes = \number_format(($bytes / $pow['mega']), $decimals, '.', '');
                $bytes .= ' MiB';
                break;
            case ($bytes >= $pow['kilo']):
                $bytes = \number_format(($bytes / $pow['kilo']), $decimals, '.', '');
                $bytes .= ' KiB';
                break;
            default:
                $bytes = \number_format($bytes, $decimals, '.', '') . ' B';
                break;
        }
        return $bytes;
    }

    /**
     * timeDifference()
     *
     * Formats the difference between two timestamps to be human readable.
     *
     * @param   int     $timestampFrom  Starting unix timestamp.
     * @param   int     $timestampTo    Ending unix timestamp.
     * @param   string  $timezone       The timezone to use. Must be a valid timezone:
     *                                  {@see http://www.php.net/manual/en/timezones.php}
     * @param   string  $append         The string to append to the difference.
     * @return  string
     */
    public static function timeDifference(int $timestampFrom, int $timestampTo = 0, string $timezone = 'UTC', string $append = ' old'): string
    {
        static $validTimezones;

        if (!$validTimezones) {
            $validTimezones = \DateTimeZone::listIdentifiers();
        }

        // Default timezone
        if (empty($timezone)) {
            $timezone = 'UTC';
        }

        // Check to see if it is a valid timezone
        if (!\in_array($timezone, $validTimezones)) {
            throw new \InvalidArgumentException('$timezone appears to be invalid.');
        }

        //
        if ($timestampTo <= 0) {
            $timestampTo = \time();
        }

        if ($timestampFrom <= 0) {
            throw new \InvalidArgumentException('$timestampFrom must be greater than 0.');
        }

        // Create \DateTime objects and set timezone
        $timestampFrom = new \DateTime(\date('Y-m-d H:i:s', $timestampFrom));
        $timestampFrom->setTimezone(new \DateTimeZone($timezone));

        $timestampTo = new \DateTime(\date('Y-m-d H:i:s', $timestampTo));
        $timestampTo->setTimezone(new \DateTimeZone($timezone));

        // Calculate difference
        $difference = $timestampFrom->diff($timestampTo);

        $string = '';

        switch (true) {
            case ($difference->y):
                $string = $difference->y . ' year(s)';
                break;
            case ($difference->m):
                $string = $difference->m . ' month(s)';
                break;
            case ($difference->d):
                if ($difference->d >= 7) {
                    $string = \ceil($difference->d / 7) . ' week(s)';
                }
                else {
                    $string = $difference->d . ' day(s)';
                }
                break;
            case ($difference->h):
                $string = $difference->h . ' hour(s)';
                break;
            case ($difference->i):
                $string = $difference->i . ' minute(s)';
                break;
            case ($difference->s):
                $string = $difference->s . ' second(s)';
                break;
        }

        // Should not happen, but if it does...
        if (empty($string)) {
            return '';
        }
        return $string . $append;
    }

    /**
     * getIpAddress()
     *
     * Return the visitor's IP address.
     *
     * @param   bool    $trustProxy  Whether or not to trust HTTP_CLIENT_IP and 
     *                               HTTP_X_FORWARDED_FOR.
     * @return  string
     */
    public static function getIpAddress(bool $trustProxy = false): string
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        if ($trustProxy) {
            return $ip;
        }

        $ips = [];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = \explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ips = \explode(',', $_SERVER['HTTP_X_REAL_IP']);
        }

        $ips = static::arrayMapDeep($ips, 'trim');

        if (!empty($ips)) {
            foreach ($ips AS $val) {
                if (\inet_ntop(\inet_pton($val)) == $val AND static::isPublicIp($val)) {
                    $ip = $val;
                    break;
                }
            }
        }
        unset($ips);

        if (!$ip AND isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
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
        return !(bool)\filter_var(
            $ipaddress, 
            \FILTER_VALIDATE_IP, 
            \FILTER_FLAG_IPV4 | \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_PRIV_RANGE
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
        return !(bool)\filter_var(
            $ipaddress, 
            \FILTER_VALIDATE_IP, 
            \FILTER_FLAG_IPV4 | \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_RES_RANGE
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
        return (bool)(!static::isPrivateIp($ipaddress) AND !static::isReservedIp($ipaddress));
    }

    /**
     * obscureEmail()
     *
     * Obscures an email address.
     *
     * @param   string  $email  Email address to obscure.
     * @return  string          Obscured email address.
     */
    public static function obscureEmail(string $email): string
    {
        // Sanity check
        if (!static::validEmail($email)) {
            throw new \InvalidArgumentException('Invalid $email specified.');
        }

        // Split and process
        $email = \str_split($email);
        $email = \array_map(function ($char) {
            return '&#' . \ord($char) . ';';
        }, $email);
        $email = \implode('', $email);

        return $email;
    }

    /**
     * currentHost()
     *
     * Determines current hostname.
     *
     * @param   bool    $stripWww  True to strip www. off the host, false to 
     *                             leave it be.
     * @return  string 
     */
    public static function currentHost(bool $stripWww = false): string
    {
        $host = \trim(\strval(($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '')));

        if (empty($host) OR !\preg_match('#^\[?(?:[a-z0-9-:\]_]+\.?)+$#', $host)) {
            $host = 'localhost';
        }

        $host = static::lower($host);

        // Strip 'www.'
        if ($stripWww) {
            return \preg_replace('#^www\.#', '', $host);
        }
        return $host;
    }

    /**
     * serverHttpVars()
     *
     * Builds an array of headers based on HTTP_* keys within $_SERVER.
     *
     * @return  array
     */
    public static function serverHttpVars(): array
    {
        $headers = [];

        if (static::doesNotContain(\PHP_SAPI, 'cli')) {
            $keys = static::arrayMapDeep(\array_keys($_SERVER), [static::class, 'lower']);
            $keys = \array_filter($keys, function ($key) {
                return (static::beginsWith($key, 'http_'));
            });

            if (!empty($keys)) {
                foreach ($keys AS $key) {
                    $headers[\strtr(
                        \ucwords(\strtr(static::substr($key, 5), '_', ' ')), 
                        ' ', 
                        '-'
                    )] = &$_SERVER[static::upper($key)];
                }
            }
            unset($keys);
        }
        return $headers;
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
        $headers = \array_map([static::class, 'lower'], static::serverHttpVars());

        // Generally, as long as HTTPS is not set or is any empty value, it is considered to be "off"
        if (
            (isset($_SERVER['HTTPS']) AND !empty($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] !== 'off') 
            OR (isset($headers['X-Forwarded-Proto']) AND $headers['X-Forwarded-Proto'] === 'https') 
            OR (isset($headers['Front-End-Https']) AND $headers['Front-End-Https'] !== 'off')
        ) {
            return true;
        }
        return false;
    }

    /**
     * currentUrl()
     *
     * Retrieve the current URL.
     *
     * @param   bool   $parse  True to return the url as an array, false otherwise.
     * @return  mixed
     */
    public static function currentUrl(bool $parse = false)
    {
        // Scheme
        $url = (static::isHttps()) ? 'https://' : 'http://';

        // Auth
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $url .= $_SERVER['PHP_AUTH_USER'];

            if (isset($_SERVER['PHP_AUTH_PW'])) {
                $url .= ":{$_SERVER['PHP_AUTH_PW']}";
            }
            $url .= '@';
        }

        // Host and port
        $url .= static::currentHost();

        $port = \intval($_SERVER['SERVER_PORT']);
        $port = ((static::isHttps() AND $port != 443) OR (!static::isHttps() AND $port != 80)) ? $port : 0;

        if (!empty($port)) {
            $url .= ":$port";
        }

        // Path
        if (!isset($_SERVER['REQUEST_URI'])) {
            $url .= \trim(\strval($_SERVER['PHP_SELF']));

            if (isset($_SERVER['QUERY_STRING'])) {
                $url .= '?' . \trim(\strval($_SERVER['QUERY_STRING']));
            }
        } else {
            $url .= \trim(\strval($_SERVER['REQUEST_URI']));
        }

        // If $parse is true, parse into array
        if ($parse) {
            $url = \parse_url($url);
        }
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

        $suffix = '';

        if (\abs($number) % 100 > 10 AND \abs($number) % 100 < 20) {
            $suffix = $suffixes[0];
        } elseif (\abs($number) % 10 < 4) {
            $suffix = $suffixes[(\abs($number) % 10)];
        } else {
            $suffix = $suffixes[0];
        }
        return $number . $suffix;
    }

    /**
     * statusHeader()
     *
     * Send a HTTP status header.
     *
     * @param  int      $code     The status code.
     * @param  string   $message  Custom status message.
     * @param  bool     $replace  True if the header should replace a previous 
     *                            similar header.
     *                            False to add a second header of the same type
     */
    public static function statusHeader(int $code = 200, string $message = '', bool $replace = true)
    {
        static $statusCodes;

        if (!$statusCodes) {
            $statusCodes = [
                100    => 'Continue',
                101    => 'Switching Protocols',
                200    => 'OK',
                201    => 'Created',
                202    => 'Accepted',
                203    => 'Non-Authoritative Information',
                204    => 'No Content',
                205    => 'Reset Content',
                206    => 'Partial Content',
                300    => 'Multiple Choices',
                301    => 'Moved Permanently',
                302    => 'Found',
                303    => 'See Other',
                304    => 'Not Modified',
                305    => 'Use Proxy',
                307    => 'Temporary Redirect',
                400    => 'Bad Request',
                401    => 'Unauthorized',
                402    => 'Payment Required',
                403    => 'Forbidden',
                404    => 'Not Found',
                405    => 'Method Not Allowed',
                406    => 'Not Acceptable',
                407    => 'Proxy Authentication Required',
                408    => 'Request Timeout',
                409    => 'Conflict',
                410    => 'Gone',
                411    => 'Length Required',
                412    => 'Precondition Failed',
                413    => 'Request Entity Too Large',
                414    => 'Request-URI Too Long',
                415    => 'Unsupported Media Type',
                416    => 'Requested Range Not Satisfiable',
                417    => 'Expectation Failed',
                422    => 'Unprocessable Entity',
                500    => 'Internal Server Error',
                501    => 'Not Implemented',
                502    => 'Bad Gateway',
                503    => 'Service Unavailable',
                504    => 'Gateway Timeout',
                505    => 'HTTP Version Not Supported'
            ];
        }

        // Sanity check
        $replace = (bool)$replace;

        if (empty($code)) {
            throw new \InvalidArgumentException('$code is invalid.');
        }

        if (empty($message)) {
            if (!isset($statusCodes[$code])) {
                throw new \Exception('No status message available. Please double check your $code or provide a custom $message.');
            }
            $message = $statusCodes[$code];
        }

        if (\headers_sent($line, $file)) {
            throw new RuntimeException(\sprintf('Failed to send header. Headers have already been sent by "%s" at line %d.', $file, $line));
        }

        //
        if (static::doesContain(\PHP_SAPI, 'cgi')) {
            \header("Status: $code $message", $replace);
        } else {
            \header(
                ($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1') . " $code $message", 
                $replace, 
                $code
            );
        }
    }

    /**
     * guid()
     *
     * Generate a Globally/Universally Unique Identifier (version 4).
     *
     * @return  string
     */
    public static function guid(): string
    {
        static $format = '%04x%04x-%04x-%04x-%04x-%04x%04x%04x';

        $guid = '';

        try {
            $guid = \sprintf(
                $format,
                static::getRandomInt(0, 0xffff),
                static::getRandomInt(0, 0xffff),
                static::getRandomInt(0, 0xffff),
                static::getRandomInt(0, 0x0fff) | 0x4000,
                static::getRandomInt(0, 0x3fff) | 0x8000,
                static::getRandomInt(0, 0xffff),
                static::getRandomInt(0, 0xffff),
                static::getRandomInt(0, 0xffff)
            );
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 0, $e);
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
     * @return  array
     */
    public static function timezoneInfo(string $timezone): array
    {
        static $validTimezones;

        if (!$validTimezones) {
            $validTimezones = \DateTimeZone::listIdentifiers();
        }

        // Check to see if it is a valid timezone
        if (empty($timezone) OR !\in_array($timezone, $validTimezones)) {
            throw new \InvalidArgumentException('$timezone appears to be invalid.');
        }

        try {
            $tz = new \DateTimeZone($timezone);
        }
        catch (\Exception $e) {
            throw new \InvalidArgumentException($e->getMessage(), 0, $e);
        }

        $info = [
            'offset'    => $tz->getOffset(new \DateTime('now', new \DateTimeZone('GMT'))) / 3600,
            'country'   => $tz->getLocation()['country_code'],
            'latitude'  => $tz->getLocation()['latitude'],
            'longitude' => $tz->getLocation()['longitude'],
            'dst'       => $tz->getTransitions($now = \time(), $now)[0]['isdst']
        ];
        unset($tz);

        return $info;
    }

    /**
     * iniGet()
     *
     * Safe ini_get taking into account its availability.
     *
     * @param   string  $option       The configuration option name.
     * @param   bool    $standardize  Standardize returned values to 1 or 0?
     * @return  mixed
     */
    public static function iniGet(string $option, bool $standardize = false)
    {
        if (!\function_exists('\\ini_get')) {
            // disabled_functions?
            throw new \RuntimeException('Native ini_get function not available.');
        }

        if (empty($option)) {
            throw new \InvalidArgumentException('$option must not be empty.');
        }

        $value = \ini_get($option);

        if ($value === false) {
            throw new \RuntimeException('$option does not exist.');
        }

        $value = (string)\trim($value);

        if ($standardize) {
            switch (static::lower($option)) {
                case 'yes':
                case 'on':
                case 'true':
                case '1':
                    $value = 1;
                    break;
                case 'no':
                case 'off':
                case 'false':
                case '0':
                default:
                    $value = 0;
                    break;
            }
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
     * @return  mixed
     */
    public static function iniSet(string $option, string $value)
    {
        if (!\function_exists('\\ini_set')) {
            // disabled_functions?
            throw new \RuntimeException('Native ini_set function not available.');
        }

        if (empty($option)) {
            throw new \InvalidArgumentException('$option must not be empty.');
        }
        return \ini_set($option, $value);
    }
}
