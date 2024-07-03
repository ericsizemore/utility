# Utility - Collection of various PHP utility functions.
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fericsizemore%2Futility.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Fericsizemore%2Futility?ref=badge_shield)
[![Build Status](https://scrutinizer-ci.com/g/ericsizemore/utility/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/utility/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/ericsizemore/utility/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/utility/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ericsizemore/utility/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/utility/?branch=master)
[![Continuous Integration](https://github.com/ericsizemore/utility/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/ericsizemore/utility/actions/workflows/continuous-integration.yml)
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

### Submitting bugs and feature requests

Bugs and feature requests are tracked on [GitHub](https://github.com/ericsizemore/utility/issues)

Issues are the quickest way to report a bug. If you find a bug or documentation error, please check the following first:

* That there is not an Issue already open concerning the bug
* That the issue has not already been addressed (within closed Issues, for example)

### Contributing

See [CONTRIBUTING](CONTRIBUTING.md).

### Author

Eric Sizemore - <admin@secondversion.com> - <https://www.secondversion.com>

### License

Utility is licensed under the MIT License - see the `LICENSE` file for details

### Acknowledgements

This library is inspired by Brandon Wamboldt's [utilphp](http://brandonwamboldt.github.com/utilphp/) library.
