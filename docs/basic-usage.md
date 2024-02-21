# Basic usage

Esi\Utility is a collection of various components that provides a toolbox, of sorts, for commonly used functions. You can 
choose to use them individually, or all in one go.

```php
// All at once
use Esi\Utility\{
    Arrays,
    Conversion,
    Dates,
    Environment,
    Filesystem,
    Image,
    Numbers,
    Strings
};
```

Then just use whichever class you need.

## Arrays Example

The Esi\Utility\Arrays class provides various array functions to handle and process arrays (or some ArrayAccess objects).

See more: [\Esi\Utility\Arrays](arrays.md)

```php
use Esi\Utility\Arrays;

$array1 = [1, 2, 3];
$array2 = ['a', 'b', 'c'];

var_dump(Arrays::interlace($array1, $array2));

/*
    Result:

    Array (
        [0] => 1
        [1] => a
        [2] => 2
        [3] => b
        [4] => 3
        [5] => c
    )
*/
```

## Conversion Example

The Esi\Utility\Conversion class provides miscellaneous conversion functions (converting between different temperatures, calculating point distance, etc.)

See more: [\Esi\Utility\Conversion](conversion.md)

```php
use Esi\Utility\Conversion;

$lat1 = 37.7749;
$lon1 = -122.4194;
$lat2 = 34.0522;
$lon2 = -118.2437;

$distance = Conversion::haversineDistance($lat1, $lon1, $lat2, $lon2);

print_r($distance);

/*
Array
(
    [meters] => 559,119
    [kilometers] => 559
    [miles] => 347
)
*/
```

## Dates Example

The Esi\Utility\Dates class provides a few functions centered around timezones. It can take a timezone and return 
certain information about that timezone, for example.

See more: [\Esi\Utility\Dates](dates.md)

```php
use Esi\Utility\Dates;

$timezone = Dates::timezoneInfo('America/New_York');

print_r($timezone);

/*
Array
(
    [offset] => -5
    [country] => US
    [latitude] => 40.71416
    [longitude] => -74.00639
    [dst] => 
)
*/
```

## Environment Example

The Esi\Utility\Environment class provides various functions centered around the server / host environment. It can be used 
to determine the current host / url, the visitor's IP address, etc.

See more: [\Esi\Utility\Environment](environment.md)

```php
use Esi\Utility\Environment;

echo Environment::host(); // example.com
echo Environment::url(); // https://example.com
var_dump(Environment::isHttps()); // bool(true)

// Need to get an ini option?
echo Environment::iniGet('display_errors');

// Or set one?
var_dump(Environment::iniSet('display_errors', 1));
```

## Filesystem Example

The Esi\Utility\Filesystem class provides various functions centered around the filesystem. It can be used 
to read and write files, get a list of files in a directory, etc.

See more: [\Esi\Utility\Filesystem](filesystem.md)

```php
use Esi\Utility\Filesystem;

echo \array_sum(Filesystem::lineCounter(__DIR__ . 'src/Arrays.php', onlyLineCount: true)); // 252
```

## Image Example

The Esi\Utility\Image class provides various functions centered around images. It can be used 
to determine if various image PHP extensions are available, determine image type, etc.

See more: [\Esi\Utility\Image](image.md)

```php
use Esi\Utility\Image;

var_dump(Image::isGdAvailable()); // bool(true)
```

## Numbers Example

The Esi\Utility\Numbers class provides a few number-centric functions.

See more: [\Esi\Utility\Numbers](numbers.md)

```php
use Esi\Utility\Numbers;

echo Numbers::randomInt(100, 200); // 150
echo Numbers::ordinal(100); // 100th
echo Numbers::sizeFormat(512); // 512 B

//... etc
```

## Strings Example

The Esi\Utility\Strings class provides various string-centric functions with the ability to use multibyte via mbstring, if available.

See more: [\Esi\Utility\Strings](strings.md)

```php
use Esi\Utility\Strings;

echo Strings::getEncoding(); // UTF-8
Strings::setEncoding('UCS-2');
echo Strings::getEncoding(); // UCS-2

var_dump(Strings::beginsWith('this is a test', 'this')); // bool(true)
//...etc
```