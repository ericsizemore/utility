# Strings

`Esi\Utility\Strings`

* [getEncoding](#getencoding)(): string
* [setEncoding](#setencoding)(string $newEncoding = '', bool $iniUpdate = false): void
* [title](#title)(string $value): string
* [lower](#lower)(string $value): string
* [upper](#upper)(string $value): string
* [substr](#substr)(string $string, int $start, ?int $length = null): string
* [lcfirst](#lcfirst)(string $string): string
* [ucfirst](#ucfirst)(string $string): string
* [strcasecmp](#strcasecmp)(string $str1, string $str2): int
* [beginsWith](#beginswith)(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool
* [endsWith](#endswith)(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool
* [doesContain](#doescontain)(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool
* [doesNotContain](#doesnotcontain)(string $haystack, string $needle, bool $insensitive = false, bool $multibyte = false): bool
* [length](#length)(string $string, bool $binarySafe = false): int
* [camelCase](#camelcase)(string $string): string
* [ascii](#ascii)(string $value): string
* [slugify](#slugify)(string $title, string $separator = '-'): string
* [randomBytes](#randombytes)(int $length): string
* [randomString](#randomstring)(int $length = 8): string
* [validEmail](#validemail)(string $email): bool
* [validJson](#validjson)(string $data): bool
* [obscureEmail](#obscureemail)(string $email): string
* [guid](#guid)(): string

#### @access private
```php
// Provides a character map to be used by ascii()
charMap(): array
```


## 



```php

```