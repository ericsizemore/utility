# Dates

`Esi\Utility\Dates`

* [timeDifference](#timedifference)(int $timestampFrom, int $timestampTo = 0, string $timezone = 'UTC', string $append = ' old', bool $extendedOutput = false): string;
* [timezoneInfo](#timezoneinfo)(string $timezone = 'UTC', bool $includeBcZones = false): array;
* [validTimezone](#validtimezone)(string $timezone, bool $includeBcZones = false): bool;
* [validateTimestamp](#validatetimestamp)(int $timestamp): bool;


## timeDifference

Formats the difference between two timestamps to be human-readable.

```php
use Esi\Utility\Dates;

$diff = Dates::timeDifference(time() - (604_800 * 5));

echo $diff; // '1 month old'

// … or with extended output:
$diff = Dates::timeDifference(time() - (604_800 * 5), extendedOutput: true);

echo $diff // '1 month 4 days old'
```

## timezoneInfo

Retrieves information about a timezone.

```php
use Esi\Utility\Dates;

$zoneInfo = Dates::timezoneInfo('America/New_York');
print_r($zoneInfo);

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

// To get information on an outdated timezone identifier, set $includeBcZones to true
$zoneInfo = Dates::timezoneInfo('EET', true);
print_r($zoneInfo);

/*
Array
(
    [offset] => 2
    [country] => N/A
    [latitude] => N/A
    [longitude] => N/A
    [dst] => 
)
*/ 
```

## validTimezone

Determines if a given timezone is valid, according to [PHP's Timezone List](http://www.php.net/manual/en/timezones.php)

```php
use Esi\Utility\Dates;

var_dump(Dates::validTimezone('InvalidTimezone')); // bool(false)
var_dump(Dates::validTimezone('America/New_York')); // bool(true)
```

## validateTimestamp

Determines if a given timestamp matches the valid range that is typically found in a unix timestamp (at least in PHP).
Typically, a timestamp for PHP can be valid if it is either 0 or between 8 and 11 digits in length.

```php
use Esi\Utility\Dates;

var_dump(Dates::validateTimestamp(123456789012)); // bool(false)
var_dump(Dates::validateTimestamp(1234567)); // bool(false)
var_dump(Dates::validateTimestamp(123456789)); // bool(true)
```
