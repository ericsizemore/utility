# Numbers

`Esi\Utility\Numbers`

* [inside](#inside)(float | int $number, float | int $min, float | int $max): bool;
* [outside](#outside)(float | int $number, float | int $min, float | int $max): bool;
* [random](#random)(int $min, int $max): int;
* [ordinal](#ordinal)(int $number): string;
* [sizeFormat](#sizeformat)(int $bytes, int $precision = 0, string $standard = 'binary'): string;


## inside

Determines if a number is inside the min and max.

```php
use Esi\Utility\Numbers;

var_dump(Numbers::inside(25, 24, 26)); // bool (true)
var_dump(Numbers::inside(25, 26, 27)); // bool (false)

var_dump(Numbers::inside(25.0, 24.0, 26.0)); // bool (true)
var_dump(Numbers::inside(25.0, 26.0, 27.0)); // bool (false)
```

## outside

Determines if a number is outside the min and max.

```php
use Esi\Utility\Numbers;

var_dump(Numbers::outside(23, 24, 26)); // bool (true)
var_dump(Numbers::outside(25, 24, 26)); // bool (false)

var_dump(Numbers::outside(23.0, 24.0, 26.0)); // bool (true)
var_dump(Numbers::outside(25.0, 24.0, 26.0)); // bool (false)
```

## random

Generate a cryptographically secure pseudo-random integer.

```php
use Esi\Utility\Numbers;

$int = Numbers::random(100, 250); // >= 100 <= 250
var_dump($int >= 100); // bool (true)
var_dump($int <= 250); // bool (true)
```

## ordinal

Retrieve the ordinal version of a number.
Basically, it will append th, st, nd, or rd based on what the number ends with.

```php
use Esi\Utility\Numbers;

echo Numbers::ordinal(1); // 1st
echo Numbers::ordinal(102); // 102nd
echo Numbers::ordinal(143); // 143rd
echo Numbers::ordinal(1_004); // 1004th
```

## sizeFormat

Format bytes to a human-readable format.

```php
use Esi\Utility\Numbers;

$sizes = [
    512 => 0,
    2_048 => 1,
    25_151_251 => 2,
    19_971_597_926 => 2,
    2_748_779_069_440 => 1,
    2_748_779_069_440 * 1_024 => 1,
    2_748_779_069_440 * (1_024 * 1_024) => 1,
];

$readable = [];

foreach ($sizes as $size => $precision) {
    $readable[] = Numbers::sizeFormat($size, $precision);
}

print_r($readable);

/*
Array (
    0 => '512 B',
    1 => '2.0 KiB',
    2 => '23.99 MiB',
    3 => '18.60 GiB'
    4 => '2.5 TiB',
    5 => '2.5 PiB',
    6 => '2.5 EiB',
)
```
