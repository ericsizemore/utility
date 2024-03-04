## CHANGELOG
A not so exhaustive list of changes for each release.

For a more detailed listing of changes between each version, 
you can use the following url: https://github.com/ericsizemore/utility/compare/v1.3.0...v2.0.0. 

Simply replace the version numbers depending on which set of changes you wish to see.

### 2.0.0 (2024-03-04)

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
  * Bump copyright year.
  * New constants added to the `Conversion` class:
```php
    /**
     * @var int EARTH_RADIUS          Earth's radius, in meters.
     * @var int METERS_TO_KILOMETERS  Used in the conversion of meters to kilometers.
     * @var int METERS_TO_MILES       Used in the conversion of meters to miles.
     */
    public const EARTH_RADIUS = 6_370_986;
    public const METERS_TO_KILOMETERS = 1000;
    public const METERS_TO_MILES = 1609.344;
```
  * These are mainly used in the `Conversion::haversineDistance()` function at the moment.

  * New constant added to the `Dates` class:
```php
    /**
     * Regex used to validate a given timestamp.
     *
     * @var string VALIDATE_TIMESTAMP_REGEX
     */
    public const VALIDATE_TIMESTAMP_REGEX = '/^\d{8,11}$/';
```
  * Added new constants to the `Environment` class:
```php
    /**
     * The default list of headers that Environment::getIpAddress() checks for.
     *
     * @var array<string> IP_ADDRESS_HEADERS
     */
    public const IP_ADDRESS_HEADERS = [
        'cloudflare' => 'HTTP_CF_CONNECTING_IP',
        'forwarded'  => 'HTTP_X_FORWARDED_FOR',
        'realip'     => 'HTTP_X_REAL_IP',
        'client'     => 'HTTP_CLIENT_IP',
        'default'    => 'REMOTE_ADDR'
    ];

    /**
     * A list of headers that Environment::host() checks to determine hostname, with a default of 'localhost'
     * if it cannot make a determination.
     *
     * @var array<string> HOST_HEADERS
     */
    public const HOST_HEADERS = [
        'forwarded' => 'HTTP_X_FORWARDED_HOST',
        'server'    => 'SERVER_NAME',
        'host'      => 'HTTP_HOST',
        'default'   => 'localhost'
    ];

    /**
     * A list of headers that Environment::url() checks for and uses to build a URL.
     *
     * @var array<string> URL_HEADERS
     */
    public const URL_HEADERS = [
        'authuser' => 'PHP_AUTH_USER',
        'authpw'   => 'PHP_AUTH_PW',
        'port'     => 'SERVER_PORT',
        'self'     => 'PHP_SELF',
        'query'    => 'QUERY_STRING',
        'request'  => 'REQUEST_URI'
    ];

    /**
     * A list of headers that Environment::isHttps() checks for to determine if current
     * environment is under SSL.
     *
     * @var array<string> HTTPS_HEADERS
     */
    public const HTTPS_HEADERS = [
        'default'   => 'HTTPS',
        'forwarded' => 'X-Forwarded-Proto',
        'frontend'  => 'Front-End-Https'
    ];

    /**
     * A list of options/headers used by Environment::requestMethod() to determine
     * current request method.
     *
     * @var array<string> REQUEST_HEADERS
     */
    public const REQUEST_HEADERS = [
        'override' => 'HTTP_X_HTTP_METHOD_OVERRIDE',
        'method'   => 'REQUEST_METHOD',
        'default'  => 'GET'
    ];
```
  * Added a new constant to the `Image` class:
```php
    /**
     * Image type/mime strings to determine image type.
     *
     * @var array<string, array<string>> IMAGE_TYPES
     */
    public const IMAGE_TYPES = [
        'jpg'  => ['image/jpg', 'image/jpeg'],
        'gif'  => ['image/gif'],
        'png'  => ['image/png'],
        'webp' => ['image/webp'],
    ];
```
  * Added new constants to the `Numbers` class:
```php
    /**
     * Ordinal suffixes.
     *
     * @var array<string> SUFFIXES
     */
    public const SUFFIXES = ['th', 'st', 'nd', 'rd'];

    /**
     * Standards units.
     *
     * @var array<string, array<string>> SIZE_FORMAT_UNITS
     */
    public const SIZE_FORMAT_UNITS = [
        'binary' => ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'],
        'metric' => ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
    ];
```
  * Updated `Strings::ascii()`, `Strings::slugify()`.
    * Added `voku/portable-ascii` as a dependency.
    * These functions now accept a new parameter: `$language`. If a language specific set of characters exists, it will use those within `slugify` and `ascii`.
  * Due to using `voku/portable-ascii` as a dependency, `Strings::charMap()` was removed.
  * Updated `StringsTest` with these changes.
  * Updated Environment, added new constants, and updated the functions/tests using them:
```php
    /**
     * Default https/http port numbers.
     *
     * @var int PORT_SECURE
     * @var int PORT_UNSECURE
     */
    public const PORT_SECURE = 443;
    public const PORT_UNSECURE = 80;

    /**
     * Regex used by Environment::host() to validate a hostname.
     *
     * @var string VALIDATE_HOST_REGEX
     */
    public const VALIDATE_HOST_REGEX = '#^\[?(?:[a-z0-9-:\]_]+\.?)+$#';

    /**
     * Maps values to their boolean equivalent for Environment::iniGet(standardize: true)
     *
     * @var array<string> BOOLEAN_MAPPINGS
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
     *
     * @var int   BINARY_STANDARD_BASE
     * @var int   METRIC_STANDARD_BASE
     * @var float CONVERSION_MODIFIER
     */
    public const BINARY_STANDARD_BASE = 1024;
    public const METRIC_STANDARD_BASE = 1000;
    public const CONVERSION_MODIFIER = 0.9;
```
  * `Arrays::exists` is now `Arrays::keyExists`
    * Added new function `Arrays:valueExists`
  * Updated `composer.json` for the `test` script, and moved all the phpunit command line options to the relevant options in the `phpunit.xml` xml config.

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
  * Reworked arrayFlatten, now has new parameter $prepend
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
