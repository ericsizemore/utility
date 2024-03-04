# Strings

`Esi\Utility\Strings`

* [getEncoding](#getencoding)(): string;
* [setEncoding](#setencoding)(string $newEncoding = '', bool $iniUpdate = false): void;
* [title](#title)(string $value): string;
* [lower](#lower)(string $value): string;
* [upper](#upper)(string $value): string;
* [substr](#substr)(string $string, int $start, ?int $length = null): string;
* [lcfirst](#lcfirst)(string $string): string;
* [ucfirst](#ucfirst)(string $string): string;
* [strcasecmp](#strcasecmp)(string $str1, string $str2): int;
* [beginsWith](#beginswith)(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool;
* [endsWith](#endswith)(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool;
* [doesContain](#doescontain)(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool;
* [doesNotContain](#doesnotcontain)(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool;
* [length](#length)(string $string, bool $binarySafe = false): int;
* [camelCase](#camelcase)(string $string): string;
* [ascii](#ascii)(string $value, string $language = 'en'): string;
* [slugify](#slugify)(string $title, string $separator = '-', string $language = 'en'): string;
* [randomBytes](#randombytes)(int $length): string;
* [randomString](#randomstring)(int $length = 8): string;
* [validEmail](#validemail)(string $email): bool;
* [validJson](#validjson)(string $data): bool;
* [obscureEmail](#obscureemail)(string $email): string;
* [guid](#guid)(): string;


## getEncoding

Returns current encoding.

```php
use Esi\Utility\Strings;

// Defaults to 'UTF-8'
echo Strings::getEncoding(); // 'UTF-8'
```

## setEncoding

Sets the encoding to use for multibyte-based functions.

```php
use Esi\Utility\Strings;

Strings::setEncoding('UCS-2');

echo Strings::getEncoding(); // 'UCS-2'
```

## title

Convert the given string to title case.

```php
use Esi\Utility\Strings;

echo Strings::title('Mary had A little lamb and She Loved it so'); // 'Mary Had A Little Lamb And She Loved It So'
```

## lower

Convert the given string to lower case.

```php
use Esi\Utility\Strings;

echo Strings::lower('tESt'); // 'test'
echo Strings::lower('TEST'); // 'test'
```

## upper

Convert the given string to upper case.

```php
use Esi\Utility\Strings;

echo Strings::upper('teSt'); // 'TEST'
```

## substr

Returns the portion of string specified by the start and length parameters.

```php
use Esi\Utility\Strings;

echo Strings::substr('abcdef', -1); // 'f'
```

## lcfirst

Convert the first character of a given string to lower case.

```php
use Esi\Utility\Strings;

echo Strings::lcfirst('Test'); // 'test'
echo Strings::lcfirst('TEST'); // 'tEST'
```

## ucfirst

Convert the first character of a given string to upper case.

```php
use Esi\Utility\Strings;

echo Strings::ucfirst('test'); // 'Test'
echo Strings::ucfirst('tEsT'); // 'TEsT'
```

## strcasecmp

Compares multibyte input strings in a binary safe case-insensitive manner.

```php
use Esi\Utility\Strings;

// Returns -1 if string1 is less than string2; 1 if string1 is greater than string2, and 0 if they are equal.
$str1 = 'test';
$str2 = 'Test';

var_dump(Strings::strcasecmp($str1, $str2)); // 0

$str1 = 'tes';
var_dump(Strings::strcasecmp($str1, $str2)); // -1

$str1 = 'testing';
var_dump(Strings::strcasecmp($str1, $str2)); // 1
```

## beginsWith

Determine if a string begins with another string.

```php
use Esi\Utility\Strings;

var_dump(Strings::beginsWith('this is a test', 'this')); // bool(true)
var_dump(Strings::beginsWith('this is a test', 'test')); // bool(false)

var_dump(Strings::beginsWith('THIS IS A TEST', 'this', true)); // bool(true)
var_dump(Strings::beginsWith('THIS IS A TEST', 'test', true)); // bool(false)

var_dump(Strings::beginsWith('THIS IS A TEST', 'this', true, true)); // bool(true)
var_dump(Strings::beginsWith('THIS IS A TEST', 'test', true, true)); // bool(false)
```

## endsWith

Determine if a string ends with another string.

```php
use Esi\Utility\Strings;

var_dump(Strings::endsWith('this is a test', 'test')); // bool(true)
var_dump(Strings::endsWith('this is a test', 'this')); // bool(false)

var_dump(Strings::endsWith('THIS IS A TEST', 'test', true)); // bool(true)
var_dump(Strings::endsWith('THIS IS A TEST', 'this', true)); // bool(false)

var_dump(Strings::endsWith('THIS IS A TEST', 'test', true, true)); // bool(true)
var_dump(Strings::endsWith('THIS IS A TEST', 'this', true, true)); // bool(false)
```

## doesContain

Determine if a string exists within another string.

```php
use Esi\Utility\Strings;


var_dump(Strings::doesContain('start a string', 'a string')); // bool(true)
var_dump(Strings::doesContain('start a string', 'starting')); // bool(false)

var_dump(Strings::doesContain('START A STRING', 'a string', true)); // bool(true)
var_dump(Strings::doesContain('START A STRING', 'starting', true)); // bool(false)

var_dump(Strings::doesContain('START A STRING', 'a string', true, true)); // bool(true)
var_dump(Strings::doesContain('START A STRING', 'starting', true, true)); // bool(false)
```

## doesNotContain

Determine if a string does not exist within another string.

```php
use Esi\Utility\Strings;

var_dump(Strings::doesNotContain('start a string', 'stringly')); // bool(true)
var_dump(Strings::doesNotContain('start a string', 'string')); // bool(false)

var_dump(Strings::doesNotContain('START A STRING', 'stringly', true)); // bool(true)
var_dump(Strings::doesNotContain('START A STRING', 'string', true)); // bool(false)

var_dump(Strings::doesNotContain('START A STRING', 'stringly', true, true)); // bool(true)
var_dump(Strings::doesNotContain('START A STRING', 'string', true, true)); // bool(false)
```

## length

Get string length.

```php
use Esi\Utility\Strings;

echo Strings::length('This is a test.'); // 15
```

## camelCase

Returns a camelCase version of the string.

```php
use Esi\Utility\Strings;

echo Strings::camelCase('background-color'); // 'backgroundColor'
echo Strings::camelCase('σamel  Case'); // 'σamelCase'
```

## ascii

Transliterate a UTF-8 value to ASCII.

```php
use Esi\Utility\Strings;

echo Strings::ascii('ăâîșțĂÂÎȘȚ', 'ro'); // 'aaistAAIST'
```

## slugify

Transforms a string into a URL or filesystem-friendly string.

```php
use Esi\Utility\Strings;

echo Strings::slugify('A simple title'); // 'a-simple-title'
echo Strings::slugify('This post -- it has a dash'); // 'this-post-it-has-a-dash'
echo Strings::slugify('123----1251251'); // '123-1251251'

echo Strings::slugify('A simple title', '_'); // 'a_simple_title'
echo Strings::slugify('This post -- it has a dash', '_'); // 'this_post_it_has_a_dash'
echo Strings::slugify('123----1251251', '_'); // '123_1251251'

echo Strings::slugify('a-simple-title'); // 'a-simple-title'
echo Strings::slugify('Țhîș îș ă șîmple țîțle', '-', 'ro'); // 'this-is-a-simple-title'
```

## randomBytes

Generate cryptographically secure pseudo-random bytes.

```php
use Esi\Utility\Strings;

echo Strings::randomBytes(8); // ’öœ,x›
```

## randomString

Generates a secure random string, based on [Strings::randomBytes()](#randombytes).

```php
use Esi\Utility\Strings;

echo Strings::randomString(8); // 710969eb
```

## validEmail

Validate an email address using PHP's built-in filter.

```php
use Esi\Utility\Strings;

var_dump(Strings::validEmail('john.smith@gmail.com')); // bool(true)
var_dump(Strings::validEmail('john.smith+label@gmail.com')); // bool(true)
var_dump(Strings::validEmail('john.smith@gmail.co.uk')); // bool(true)
var_dump(Strings::validEmail('j@')); // bool(false)
```

## validJson

Determines if a string is valid JSON.

**Deprecated as of 2.0.0**

```php
use Esi\Utility\Strings;

var_dump(Strings::validJson('{ "test": { "foo": "bar" } }')); // bool(true)
var_dump(Strings::validJson('{ "": "": "" } }')); // bool(false)
```

## obscureEmail

Obscures an email address.

```php
use Esi\Utility\Strings;

echo Strings::obscureEmail('admin@secondversion.com');
/*
'&#97;&#100;&#109;&#105;&#110;&#64;&#115;&#101;&#99;&#111;&#110;&#100;&#118;&#101;&#114;&#115;&#105;&#111;&#110;&#46;&#99;&#111;&#109;'
*/

```

## guid

Generate a Globally/Universally Unique Identifier (version 4).

```php
use Esi\Utility\Strings;

echo Strings::guid(); // b7c2b60e-dda4-42e9-9ea0-076a28911cea
```
