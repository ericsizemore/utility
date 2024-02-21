# Conversion

`Esi\Utility\Conversion`

* [fahrenheitToCelsius](#fahrenheittocelsius)(float $fahrenheit, bool $rounded = true, int $precision = 2): float;
* [celsiusToFahrenheit](#celsiustofahrenheit)(float $celsius, bool $rounded = true, int $precision = 2): float;
* [celsiusToKelvin](#celsiustokelvin)(float $celsius, bool $rounded = true, int $precision = 2): float;
* [kelvinToCelsius](#kelvintocelsius)(float $kelvin, bool $rounded = true, int $precision = 2): float;
* [fahrenheitToKelvin](#fahrenheittokelvin)(float $fahrenheit, bool $rounded = true, int $precision = 2): float;
* [kelvinToFahrenheit](#kelvintofahrenheit)(float $kelvin, bool $rounded = true, int $precision = 2): float;
* [fahrenheitToRankine](#fahrenheittorankine)(float $fahrenheit, bool $rounded = true, int $precision = 2): float;
* [rankineToFahrenheit](#rankinetofahrenheit)(float $rankine, bool $rounded = true, int $precision = 2): float;
* [celsiusToRankine](#celsiustorankine)(float $celsius, bool $rounded = true, int $precision = 2): float;
* [rankineToCelsius](#rankinetocelsius)(float $rankine, bool $rounded = true, int $precision = 2): float;
* [kelvinToRankine](#kelvintorankine)(float $kelvin, bool $rounded = true, int $precision = 2): float;
* [rankineToKelvin](#rankinetokelvin)(float $rankine, bool $rounded = true, int $precision = 2): float;
* [haversineDistance](#haversinedistance)(int|float $startingLatitude, int|float $startingLongitude, int|float $endingLatitude, int|float $endingLongitude, int $precision = 0): array;


## fahrenheitToCelsius

Convert Fahrenheit (Fº) To Celsius (Cº)

```php
use Esi\Utility\Conversion;

echo Conversion::fahrenheitToCelsius(74); // 23.33
```

## celsiusToFahrenheit

Convert Celsius (Cº) To Fahrenheit (Fº)

```php
use Esi\Utility\Conversion;

echo Conversion::celsiusToFahrenheit(23.33); // 73.99
```

## celsiusToKelvin

Convert Celsius (Cº) To Kelvin (K)

```php
use Esi\Utility\Conversion;

echo Conversion::celsiusToKelvin(23.33); // 296.48
```

## kelvinToCelsius

Convert Kelvin (K) To Celsius (Cº)

```php
use Esi\Utility\Conversion;

echo Conversion::kelvinToCelsius(296.48); // 23.33
```

## fahrenheitToKelvin

Convert Fahrenheit (Fº) To Kelvin (K)

```php
use Esi\Utility\Conversion;

echo Conversion::fahrenheitToKelvin(74)); // 296.48
```

## kelvinToFahrenheit

Convert Kelvin (K) To Fahrenheit (Fº)

```php
use Esi\Utility\Conversion;

echo Conversion::kelvinToFahrenheit(296.48); // 73.99
```

## fahrenheitToRankine

Convert Fahrenheit (Fº) To Rankine (ºR)

```php
use Esi\Utility\Conversion;

echo Conversion::fahrenheitToRankine(74); // 533.67
```

## rankineToFahrenheit

Convert Rankine (ºR) To Fahrenheit (Fº)

```php
use Esi\Utility\Conversion;

echo Conversion::rankineToFahrenheit(533.67); // 74.0
```

## celsiusToRankine

Convert Celsius (Cº) To Rankine (ºR)

```php
use Esi\Utility\Conversion;

echo Conversion::celsiusToRankine(30); // 545.67
```

## rankineToCelsius

Convert Rankine (ºR) To Celsius (Cº)

```php
use Esi\Utility\Conversion;

echo Conversion::rankineToCelsius(545.67); // 30.0
```

## kelvinToRankine

Convert Kelvin (K) To Rankine (ºR)

```php
use Esi\Utility\Conversion;

echo Conversion::kelvinToRankine(130); // 234.0
```

## rankineToKelvin

Convert Rankine (ºR) To Kelvin (K)

```php
use Esi\Utility\Conversion;

echo Conversion::rankineToKelvin(234.0); // 130.0
```

## haversineDistance

Calculate the distance between two points using the Haversine Formula.

See [`Haversine_formula#Formulation`](https://en.wikipedia.org/wiki/Haversine_formula#Formulation).

```php
use Esi\Utility\Conversion;

$lat1 = 37.774_9;
$lon1 = -122.419_4;
$lat2 = 34.052_2;
$lon2 = -118.243_7;

$result = Conversion::haversineDistance($lat1, $lon1, $lat2, $lon2);

print_r($result);

/*
Array
(
    [meters] => 559,119
    [kilometers] => 559
    [miles] => 347
)
*/
```
