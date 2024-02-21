# Environment

`Esi\Utility\Environment`

* [requestMethod](#requestmethod)(): string;
* [var](#var)(string $var, string | int | null $default = ''): string | int | null;
* [ipAddress](#ipaddress)(bool $trustProxy = false): string;
* [isPrivateIp](#isprivateip)(string $ipaddress): bool;
* [isReservedIp](#isreservedip)(string $ipaddress): bool;
* [isPublicIp](#ispublicip)(string $ipaddress): bool;
* [host](#host)(bool $stripWww = false, bool $acceptForwarded = false): string;
* [isHttps](#ishttps)(): bool;
* [url](#url)(): string;
* [iniGet](#iniget)(string $option, bool $standardize = false): string | false;
* [iniSet](#iniset)(string $option, string $value): string | false;


## requestMethod

Gets the request method.

```php
use Esi\Utility\Environment;

// with $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'GET'
// and $_SERVER['REQUEST_METHOD'] to say, 'POST' for example
echo Environment::requestMethod(); // GET

```

## var

Gets a variable from $_SERVER using $default if not provided.

```php
use Esi\Utility\Environment;

// var to check for in $_SERVER, default value
echo Environment::var('HTTP_HOST', 'localhost'); // example.com
```

## ipAddress

Return the visitor's IP address.

```php
use Esi\Utility\Environment;

/**
 * If, as an example:
 *    $_SERVER['REMOTE_ADDR'] = '1.1.1.1';
 *    $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.1.1.3';
 */
echo Environment::getIpAddress(); // 1.1.1.1
echo Environment::getIpAddress(true); // 1.1.1.3

```

## isPrivateIp

Determines if an IP address is within the private range.

```php
use Esi\Utility\Environment;

var_dump(Environment::isPrivateIp('192.168.0.0')); // bool(true)
```

## isReservedIp

Determines if an IP address is within the reserved range.

```php
use Esi\Utility\Environment;

var_dump(Environment::isReservedIp('0.255.255.255')); // bool(true)
```

## isPublicIp

Determines if an IP address is not within the private or reserved ranges.

```php
use Esi\Utility\Environment;

var_dump(Environment::isPublicIp('1.1.1.1')); // bool(true)
```

## host

Determines current hostname.

```php
use Esi\Utility\Environment;

/**
 * If, for example:
 * $_SERVER['HTTP_HOST'] = 'example.com';
 * $_SERVER['HTTP_X_FORWARDED_HOST'] = 'example2.com';
 */
echo Environment::host(false, true); // example2.com
echo Environment::host(); // example.com
```

## isHttps

Checks to see if SSL is in use.

```php
use Esi\Utility\Environment;

// If $_SERVER['HTTPS'] is null
var_dump(Environment::isHttps()); // bool(false)

// if $_SERVER['HTTPS'] is 'on'
var_dump(Environment::isHttps()); // bool(true)

// If $_SERVER['HTTPS'] is null but $_SERVER['HTTP_X_FORWARDED_PROTO'] is set and is 'https'
var_dump(Environment::isHttps()); // bool(true)
```

## url

Retrieve the current URL.

```php
use Esi\Utility\Environment;

/**
 * If, for example, URL is 'http://test.dev/test.php?foo=bar'
 *
 * Then we assume the server variables would contain:
 *     $_SERVER['HTTP_HOST'] = 'test.dev';
 *     $_SERVER['REQUEST_URI'] = '/test.php?foo=bar';
 *     $_SERVER['QUERY_STRING'] = 'foo=bar';
 *     $_SERVER['PHP_SELF'] = '/test.php';
 */
echo Environment::url(); // http://test.dev/test.php?foo=bar

```

## iniGet

Safe ini_get taking into account its availability.

```php
use Esi\Utility\Environment;

// Let's say display_errors is set to 1.
echo (int) Environment::iniGet('display_errors', true); // 1

// What if we try passing an empty string?
echo Environment::iniGet(''); // Results in \RuntimeException

// Or an invalid/not existent option?
echo Environment::iniGet('this_should_notbe_a_valid_option'); // Results in \RuntimeException
```

## iniSet

Safe ini_set taking into account its availability.

```php
use Esi\Utility\Environment;

// Capture old value
$oldValue = Environment::iniSet('display_errors', Environment::iniGet('display_errors'));

var_dump($oldValue === Environment::iniSet('display_errors', $oldValue)); // bool(true)

// This results in an \ArgumentCountError
var_dump(Environment::iniSet(''));

// This results in an \InvalidArgumentException
var_dump(Environment::iniSet('', ''));
```
