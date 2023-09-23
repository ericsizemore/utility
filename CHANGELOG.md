## CHANGELOG
A not so exhaustive list of changes for each release.

For a more detailed listing of changes between each version, 
you can use the following url: https://github.com/ericsizemore/utility/compare/v1.1.1...v1.1.2. 

Simply replace the version numbers depending on which set of changes you wish to see.

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
