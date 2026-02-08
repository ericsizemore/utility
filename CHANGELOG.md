# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [Unreleased]

### Changed

  * Updated dev-dependencies.
  * Updated security policy in `SECURITY.md`.
  * Updated the `continuous integration` workflow for the Psalm static analysis.
  * Updated the `backward compatibility` promise (see [backward-compatibility.md](backward-compatibility.md))


## [2.2.0] - 2025-01-10

> [!IMPORTANT]
> Initially, in the previous `2.1.0` release, several functions were deprecated (see `Deprecated` under `2.1.0` [below](CHANGELOG.md#deprecated)).
> After much thought on the direction I want this library to move in, I am reversing my decision to deprecate the Array and Temperature functions mentioned.


### Added

  * `Dates::formatDifferenceOutput()` a private function to handle formatting the output for `Dates::timeDifference()`
  * `Dates::INTERVAL_UNITS` constant.
  * Added new parameter `$includeBcZones` to `Dates::timezoneInfo()` and `Dates::validTimezone()`
      * If true, includes all backwards compatible (and outdated) timezones.
  * Added `Psalm` and `RectorPHP` as dev-dependencies.

### Changed

  * `Arrays::flatten()`, `Arrays::mapDeep()`, and all the temperature related functions in `Conversions` are no longer deprecated.
  * New tests added for the `Image` class. Still a work in progress; working toward no 'codeCoverageIgnore'.
  * `Dates::timeDifference()` logic was changed and a new parameter `$extendedOutput` added.
    * If this parameter is `false` (default), it returns the same output as usual.
    * If this parameter is `true`, it formats the output with available non-zero `DateInterval` units. For example: 2 days 2 hours 20 minutes old.
  * BC BREAK: `Dates::timeDifference()` output no longer uses '(s)'. Will add 's' if greater than one.
  * `Arrays` class has gone through a bit of a refactor:
    * `Arrays::mapDeep()` updated to avoid circular references when dealing with objects.
      * New PHPUnit tests added to cover `mapDeep()` as well.
    * Updated the `Arrays` class for psalm/phpstan templates/generics.
      * Still something I am admittedly not highly experienced with, so it may look/feel a little wonky. Going to improve it as I can.
  * Optimized the `Conversion::haversineDistance()` method.
  * Refactored unit tests.
  * Changes throughout to make fixes and improvements reported by Psalm.


## [2.1.0] - 2024-04-23

This release focuses on setting up the future of the library. No new features or functionality has been added in this release.

### Added

  * Added mend.io's Renovate to workflow
  * Added `Esi\Clock` to dependencies, updated `Esi\Utility\Dates` accordingly.
  * Added a code of conduct.
  * Added a backwards compatibility promise.

### Changed

  * Reformatted this CHANGELOG to be more in line with the [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) format.
  * Implemented various PHPCS-Fixer recommendations.
  * Updated coding style via `.php-cs-fixer.dist.php`, and as a result, implemented those changes throughout the library.
  * Changed the header being used for all files to something smaller, and more simple.
  * Bumped version requirement for the PHPUnit dev-dependency to 11.1
  * Updated `tests` github workflow to add uploading coverage data to Codecov.io
  * Made all utility classes `abstract` as they never need instantiation.
  * Separate contributing information into its own file.
    * Updated contributing information and guidelines.

### Deprecated

The next major release, v3, will move to PHP 8.3 as a minimum requirement.

  * `Arrays::flatten()`'s current signature and functionality will change in the next major release (v3.0)
  * `Arrays::mapDeep()`'s current signature and functionality will change in the next major release (v3.0)
    * The functionality of mapping the properties of an object will likely be moved to its own utility class/function.
  * `Arrays::validJson()` will be removed in the next major release (v3.0)
  * All the temperature related functions in `Conversions`:
    * Current signature and functionality deprecated, will be changed in 3.0

### Removed

  * Removed deprecated `Arrays::exists`
  * Removed all instances of `@phpstan-ignore-*` opting to instead use a baseline for PHPStan (`phpstan-baseline.neon`)


## [2.0.0] - 2024-03-04

Utility has undergone a complete restructuring, wherein it is no longer a single super class. The class has been broken down into smaller classes or "components".

### Added

  * Utility\Arrays
  * Utility\Conversion
  * Utility\Dates
  * Utility\Environment
  * Utility\Filesystem
  * Utility\Image
  * Utility\Numbers
  * Utility\Strings
  * New constants:
    * `Conversion::EARTH_RADIUS`
    * `Conversion::METERS_TO_KILOMETERS`
    * `Conversion::METERS_TO_MILES`
    * `Dates::VALIDATE_TIMESTAMP_REGEX`
    * `Environment::IP_ADDRESS_HEADERS`
    * `Environment::HOST_HEADERS`
    * `Environment::URL_HEADERS`
    * `Environment::HTTPS_HEADERS`
    * `Environment::REQUEST_HEADERS`
    * `Image::IMAGE_TYPES`
    * `Numbers::SUFFIXES`
    * `Numbers::SIZE_FORMAT_UNITS`
    * `Numbers::BINARY_STANDARD_BASE`
    * `Numbers::METRIC_STANDARD_BASE`
    * `Numbers::CONVERSION_MODIFIER`
    * `Environment::PORT_SECURE`
    * `Environment::PORT_UNSECURE`
    * `Environment::VALIDATE_HOST_REGEX`
    * `Environment::BOOLEAN_MAPPINGS`
  * Added new function `Arrays:valueExists`

### Changed

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
  * Updated `Strings::ascii()`, `Strings::slugify()`.
    * Added `voku/portable-ascii` as a dependency.
    * These functions now accept a new parameter: `$language`. If a language specific set of characters exists, it will use those within `slugify` and `ascii`.
  * `Arrays::exists` is now `Arrays::keyExists`
  * Updated `composer.json` for the `test` script, and moved all the phpunit command line options to the relevant options in the `phpunit.xml` xml config.
  * Updated unit tests.

### Deprecated

  * `Arrays::exists()` will be replaced with `Arrays::keyExists()`

### Removed

  * Utility\Utility super class.
  * Due to using `voku/portable-ascii` as a dependency, `Strings::charMap()` was removed.


## [1.3.0] - 2023-12-11

### Changed

  * `currentUrl()` no longer has any parameters, and just returns the URL string.
    * `currentUrl(bool $parse = false)` is now just `currentUrl()`
    * Just run `parse_url(Utility::currentUrl())` if that functionality is needed.
  * Code cleanup per PHPCS and PHPScrutinizer
    * Small refactoring of some functions, such as `currentUrl` and `isReallyWritable`
  * Attempt at increasing test coverage.
  * Further cleanup to try and adhere to PSR-12.
    * Adding information into `README.md` for PHPDoc related guidelines, in an effort
      to adhere to `PSR-5` and `PSR-19`.

### Deprecated

  * `serverHttpVars()` deprecated, just use getallheaders() instead.

### Removed

  * Removed deprecated `statusHeader()`
    * Added `ralouphie/getallheaders` as a polyfill, for situations where the SAPI is not Apache
    * `serverHttpVars()` will just return the `getallheaders()` output


## [1.2.0] - 2023-09-22

### Added

  * New function `arrayInterlace`
  * New functions for temperature conversions:
    * `fahrenheitToCelsius`, `celsiusToFahrenheit`, `celsiusToKelvin`, `kelvinToCelsius`, `fahrenheitToKelvin`, `kelvinToFahrenheit`
    * `fahrenheitToRankine`, `rankineToFahrenheit`, `celsiusToRankine`, `rankineToCelsius`, `kelvinToRankine`, `rankineToKelvin`
  * Initial implementation of PHPUnit and the testing suite.

### Changed

  * Code cleanup per PHPStan (level: 9, strict, bleeding edge)
  * `doesContain()` and `doesNotContain()` now uses the PHP 8 native `str_contains()` function.
    * No longer relies on `mbstring` for these two functions, as it does not appear to be necessary.
    * However, you can pass true to the `$multibyte` parameter to use `mbstring`
    * New signature: `(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false)`
  * `beginsWith()` and `endsWith()` now uses the PHP 8 native `str_starts_with()` and `str_ends_with()` functions.
    * No longer relies on `mbstring` for these two functions, as it does not appear to be necessary.
    * However, you can pass true to the `$multibyte` parameter to use mbstring
    * New signature: `(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false)`
  * Reworked `arrayFlatten`, now has new parameter `$prepend`
  * `validJson()` should now return properly

### Deprecated

  * statusHeader() is now deprecated, you can use PHP's built-in http_response_code function instead.


## [1.1.2] - 2023-08-17

### Changed

  * Version bump
  * Minor code cleanup


## [1.1.1] - 2023-08-11

### Changed

  * Version bump
  * Cleaning up issues per PHPStan


## [1.1.0] - 2023-06-24

### Changed

  * Version bump
  * Bumped PHP version requirement to 8.2
  * Updated composer.json
  * BC BREAK: randomBytes, randomInt, randomString and guid now throw \Random\RandomException

### Fixed

  * FIX: Minor documentation improvements.
  * FIX: Minor code improvements.


### [1.0.3] - 2023-06-03

### Changed

  * Updated copyright year(s) and version bump.
  * Bumped PHP version requirement.
  * Updated composer.json

### Fixed

  * FIX: Minor documentation improvements.
  * FIX: Minor code improvements.
  * FIX: Some functions not defined/used properly.

### [1.0.2] - 2021-08-20

### Changed

  * Updated copyright year(s) and version bump.

### Fixed

  * FIX: Minor documentation improvements.


### [1.0.1] - 2019-04-07

### Added

  * FEAT: New functions - lcfirst, ucfirst, strcasecmp

### Fixed

  * FIX: Minor documentation improvements.


### [1.0.0] - 2017-02-08

  * Initial release


[unreleased]: https://github.com/ericsizemore/utility/tree/master
[2.2.0]: https://github.com/ericsizemore/utility/releases/tag/v2.2.0
[2.1.0]: https://github.com/ericsizemore/utility/releases/tag/v2.1.0
[2.0.0]: https://github.com/ericsizemore/utility/releases/tag/v2.0.0
[1.3.0]: https://github.com/ericsizemore/utility/releases/tag/v1.3.0
[1.2.0]: https://github.com/ericsizemore/utility/releases/tag/v1.2.0
[1.1.2]: https://github.com/ericsizemore/utility/releases/tag/v1.1.2
[1.1.1]: https://github.com/ericsizemore/utility/releases/tag/v1.1.1
[1.1.0]: https://github.com/ericsizemore/utility/releases/tag/v1.1.0
[1.0.3]: https://github.com/ericsizemore/utility/releases/tag/v1.0.3
