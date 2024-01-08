## CHANGELOG
A not so exhaustive list of changes for each release.

For a more detailed listing of changes between each version, 
you can use the following url: https://github.com/ericsizemore/utility/compare/v1.3.0...v2.0.0. 

Simply replace the version numbers depending on which set of changes you wish to see.

### 2.0.0 ()

#### -dev 2024-01-08
  * Updated Environment, added new constants, and updated the functions/tests using them:
```php
    /**
     * Default https/http port numbers.
     *
     * @var int
     */
    public const PORT_SECURE = 443;
    public const PORT_UNSECURE = 80;

    /**
     * Regex used by Environment::host() to validate a hostname.
     *
     * @var string
     */
    public const VALIDATE_HOST_REGEX = '#^\[?(?:[a-z0-9-:\]_]+\.?)+$#';

    /**
     * Maps values to their boolean equivalent for Environment::iniGet(standardize: true)
     *
     * @var array<string>
     */
    public const BOOLEAN_MAPPINGS = [
        'yes'   => '1',
        'on'    => '1',
        'true'  => '1',
        '1'     => '1',
        'no'    => '0',
        'off'   => '0',
        'false' => '0',
        '0'     => '0',
    ];
```
  * Updated Numbers, added new constants:
```php
    /**
     * Constants for Numbers::sizeFormat(). Sets bases and modifier for the conversion.
     */
    public const BINARY_STANDARD_BASE = 1024;
    public const METRIC_STANDARD_BASE = 1000;
    public const CONVERSION_MODIFIER = 0.9;
```
  * Forked [Crell\EnumTools](https://github.com/Crell/EnumTools)
    * Updated namespaces, file names, locations
    * Crell\EnumTools\Http => Esi\Utility\Enums\Http
    * Status => StatusCodes
      * message() => getMessage()
      * category() => getCategory()
      * Added getValue(), getName()
    * StatusCategory => StatusCodeCategories
      * Added getValue(), getName()
    * Method => Methods
      * Added getValue(), getName()
    * Added StatusCodeDescriptions
      * Can give a description of a particular status code, per the MDN (Mozilla Developer Network) definitions.
    * Small bug fixes, coding standards updates, etc.
    * Added unit tests

#### -dev 2024-01-07
  * Utility has undergone a complete restructuring.
  * src/Utility/Utility.php no longer exists. The class has been broken down into smaller classes or "components":
    * src/Utility/Arrays.php
    * src/Utility/Conversion.php
    * src/Utility/Dates.php
    * src/Utility/Environment.php
    * src/Utility/Filesystem.php
    * src/Utility/Image.php
    * src/Utility/Numbers.php
    * src/Utility/Strings.php
  * Made a great improvement in code coverage/testing
  * Effort to improve documentation, which can be found in `docs/` or online [here](https://www.secondversion.com/docs/utility)
  * Filesystem::lineCounter() (Utility::lineCounter() in Utility < 2.0) no longer has a `$skipEmpty` parameter. It will now always skip empty lines.
    * Replaced the use of `file()` with `SplFileObject` and flags `SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE`
      * Should be more efficient, especially for larger files.
  * Numbers::sizeFormat() (Utility::sizeFormat() in Utility < 2.0) has a new option:
    * Numbers::sizeFormat(int $bytes, int $precision = 0, string $system = 'binary'): string
    * System can be one of 'binary' or 'metric' and it determines the base/mod for the formatting.
  * Updated tests to use PHPUnit's CoversClass and DataProvider attributes.
    * Changed `$this` to `self::` in tests when calling PHPUnit methods
  * Updated composer.json to support PHP 8.2 - 8.4
    * Added PHPStan strict rules to dev dependencies
    * Updated workflows to introduce testing on PHP 8.4
    * Added RectorPHP and PHP-CS-Fixer to dev dependencies
  * Bump copyright year.

## Branch [1.3.x](https://github.com/ericsizemore/utility/tree/1.3.x) Changelog

### 1.3.0 (2023-12-11)
  * currentUrl() no longer has any parameters, and just returns the URL string.
    * currentUrl(bool $parse = false) is now just currentUrl()
    * Just run parse_url(Utility::currentUrl()) if that functionality is needed.
  * serverHttpVars() deprecated, just use getallheaders() instead.
    * Added ralouphie/getallheaders as a polyfill, for situations where the SAPI is not Apache
    * serverHttpVars() will just return the getallheaders() output
  * Removed deprecated statusHeader()
  * Code cleanup per PHPCS and PHPScrutinizer
    * Small refactoring of some functions, such as currentUrl and isReallyWritable
  * Attempt at increasing test coverage.
  * Further cleanup to try and adhere to PSR-12.
    * Adding information into README.md for PHPDoc related guidelines, in an effort
      to adhere to PSR-5 and PSR-19.

### 1.2.0 (2023-09-22)
  * Code cleanup per PHPStan (level: 9, strict, bleeding edge)
  * New function arrayInterlace
  * doesContain() and doesNotContain() now uses the PHP 8 native str_contains() function.
    * No longer relies on mbstring for these two functions, as it does not appear to be necessary.
    * However, you can pass true to the $multibyte parameter to use mbstring
    * New signature: `(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false)`
  * beginsWith() and endsWith() now uses the PHP 8 native str_starts_with() and str_ends_with() functions.
    * No longer relies on mbstring for these two functions, as it does not appear to be necessary.
    * However, you can pass true to the $multibyte parameter to use mbstring
    * New signature: `(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false)`
  * New functions for temperature conversions:
    * fahrenheitToCelsius, celsiusToFahrenheit, celsiusToKelvin, kelvinToCelsius, fahrenheitToKelvin, kelvinToFahrenheit
    * fahrenheitToRankine, rankineToFahrenheit, celsiusToRankine, rankineToCelsius, kelvinToRankine, rankineToKelvin
  * Reworked arrayFlatten, now has new paramater $prepend
  * statusHeader() is now deprecated, you can use PHP's built-in http_response_code function instead.
  * validJson() should now return properly
  * Initial implementation of PHPUnit and the testing suite.

### 1.1.2 (2023-08-17)
  * Version bump
  * Minor code cleanup

### 1.1.1 (2023-08-11)
  * Version bump
  * Cleaning up issues per PHPStan

### 1.1.0 (2023-06-24)
  * Version bump
  * Bumped PHP version requirement to 8.2
  * Updated composer.json
  * FIX: Minor documentation improvements.
  * FIX: Minor code improvements.
  * BC BREAK: randomBytes, randomInt, randomString and guid now throw \Random\RandomException

### 1.0.3 (2023-06-03)
  * Updated copyright year(s) and version bump.
  * Bumped PHP version requirement.
  * Updated composer.json
  * FIX: Minor documentation improvements.
  * FIX: Minor code improvements.
  * FIX: Some functions not defined/used properly.

### 1.0.2 (2021-08-20)
  * Updated copyright year(s) and version bump.
  * FIX: Minor documentation improvements.

### 1.0.1 (2019-04-07)

  * FEATURE: New functions - lcfirst, ucfirst, strcasecmp
  * FIX: Minor documentation improvements.

### 1.0.0 (2017-02-08)

  * Initial release
