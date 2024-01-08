# Utility - Collection of various PHP utility functions.
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fericsizemore%2Futility.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Fericsizemore%2Futility?ref=badge_shield)
[![Build Status](https://scrutinizer-ci.com/g/ericsizemore/utility/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/utility/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/ericsizemore/utility/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/utility/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ericsizemore/utility/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/utility/?branch=master)
[![Tests](https://github.com/ericsizemore/utility/actions/workflows/tests.yml/badge.svg)](https://github.com/ericsizemore/utility/actions/workflows/tests.yml)
[![PHPStan](https://github.com/ericsizemore/utility/actions/workflows/main.yml/badge.svg)](https://github.com/ericsizemore/utility/actions/workflows/main.yml)

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

There are also useful `Enum`'s provided, with the following currently available:

#### `Http`

  * [Methods](docs/enums/http/methods.md)
  * [StatusCodes](docs/enums/http/status-codes.md)
  * [StatusCodeCategories](docs/enums/http/status-code-categories.md)
  * [StatusCodeDescriptions](docs/enums/http/status-code-descriptions.md)

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

Utility accepts contributions of code and documentation from the community. 
These contributions can be made in the form of Issues or [Pull Requests](http://help.github.com/send-pull-requests/) on the [Utility repository](https://github.com/ericsizemore/utility).

Utility is licensed under the MIT license. When submitting new features or patches to Utility, you are giving permission to license those features or patches under the MIT license.

Utility tries to adhere to PHPStan level 9 with strict rules and bleeding edge. Please ensure any contributions do as well.

#### Guidelines

Before we look into how, here are the guidelines. If your Pull Requests fail to pass these guidelines it will be declined and you will need to re-submit when you’ve made the changes. This might sound a bit tough, but it is required for me to maintain quality of the code-base.

#### PHP Style

Please ensure all new contributions match the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style guide. The project is not fully PSR-12 compatible, yet; however, to ensure the easiest transition to the coding guidelines, I would like to go ahead and request that any contributions follow them.

#### Documentation

If you change anything that requires a change to documentation then you will need to add it. New methods, parameters, changing default values, adding constants, etc are all things that will require a change to documentation. The change-log must also be updated for every change. Also PHPDoc blocks must be maintained.

##### Documenting functions/variables (PHPDoc)

Please ensure all new contributions adhere to:
  * [PSR-5 PHPDoc](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md)
  * [PSR-19 PHPDoc Tags](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md)

when documenting new functions, or changing existing documentation.

#### Branching

One thing at a time: A pull request should only contain one change. That does not mean only one commit, but one change - however many commits it took. The reason for this is that if you change X and Y but send a pull request for both at the same time, we might really want X but disagree with Y, meaning we cannot merge the request. Using the Git-Flow branching model you can create new branches for both of these features and send two requests.

### Author

Eric Sizemore - <admin@secondversion.com> - <https://www.secondversion.com>

### License

Utility is licensed under the MIT License - see the `LICENSE` file for details


[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fericsizemore%2Futility.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fericsizemore%2Futility?ref=badge_large)

### Acknowledgements

This library is inspired by Brandon Wamboldt's [utilphp](http://brandonwamboldt.github.com/utilphp/) library.
