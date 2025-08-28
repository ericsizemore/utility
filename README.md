# Utility - Collection of various PHP utility functions.
[![Build Status](https://scrutinizer-ci.com/g/ericsizemore/utility/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/utility/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/ericsizemore/utility/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/utility/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ericsizemore/utility/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/utility/?branch=master)
[![Continuous Integration](https://github.com/ericsizemore/utility/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/ericsizemore/utility/actions/workflows/continuous-integration.yml)
[![Type Coverage](https://shepherd.dev/github/ericsizemore/utility/coverage.svg)](https://shepherd.dev/github/ericsizemore/utility)
[![Psalm Level](https://shepherd.dev/github/ericsizemore/utility/level.svg)](https://shepherd.dev/github/ericsizemore/utility)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=ericsizemore_utility&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=ericsizemore_utility)
[![Latest Stable Version](https://img.shields.io/packagist/v/esi/utility.svg)](https://packagist.org/packages/esi/utility)
[![Downloads per Month](https://img.shields.io/packagist/dm/esi/utility.svg)](https://packagist.org/packages/esi/utility)
[![License](https://img.shields.io/packagist/l/esi/utility.svg)](https://packagist.org/packages/esi/utility)


[Utility](http://github.com/ericsizemore/utility/) is a small collection of useful functions that are aimed to make developers' lives just a tad bit easier.

## Installation

### Composer

Install the latest version with:

```bash
$ composer require esi/utility
```

Then, within your project (if not already included), include composer's autoload. For example:

```php
<?php

require 'vendor/autoload.php';

?>
```

## Basic Usage

Utility is a set of classes that are broken down into several "components":

#### `Utilities`

  * [Arrays](docs/arrays.md)
  * [Conversion](docs/conversion.md)
  * [Dates](docs/dates.md)
  * [Environment](docs/environment.md)
  * [Filesystem](docs/filesystem.md)
  * [Image](docs/image.md)
  * [Numbers](docs/numbers.md)
  * [Strings](docs/strings.md)

As an example, let's say you want to convert a string to title case. To do so:
```php
<?php

use Esi\Utility\Strings;

$title = Strings::title('this is my title');

echo $title;

?>
```

All methods of found within Utility's classes are static. So, for example, to retrieve the information for a particular timezone:

```php
<?php

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

?>
```

## Documentation
Please see [docs](/docs) or view [online](https://www.secondversion.com/docs/utility/).

## About

### Requirements

- Utility works with PHP 8.2.0 or above.

### Credits

- [Eric Sizemore](https://github.com/ericsizemore)
- [All Contributors](https://github.com/ericsizemore/utility/contributors)
- Special thanks to Brandon Wamboldt and their [utilphp](https://brandonwamboldt.github.io/utilphp/) library for the inspiration for `Esi\Utility`.

### Contributing

See [CONTRIBUTING](./CONTRIBUTING.md).

Bugs and feature requests are tracked on [GitHub](https://github.com/ericsizemore/utility/issues).

### Contributor Covenant Code of Conduct

See [CODE_OF_CONDUCT.md](./CODE_OF_CONDUCT.md)

### Backward Compatibility Promise

See [backward-compatibility.md](./backward-compatibility.md) for more information on Backwards Compatibility.

### Changelog

See the [CHANGELOG](./CHANGELOG.md) for more information on what has changed recently.

### License

See the [LICENSE](./LICENSE.md) for more information on the license that applies to this project.

### Security

See [SECURITY](./SECURITY.md) for more information on the security disclosure process.
