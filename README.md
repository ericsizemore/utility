# Utility - Collection of various PHP utility functions.

[Utility](http://github.com/ericsizemore/utility/) is (currently) a small collection of 
useful functions that are aimed to make developers' lives just a tad bit easier.

## Installation

### Composer

**WIP** Install the latest version with

```bash
$ composer require esi/utility
```

### Standalone File

Simply drop `Utility.php` in any project and call `include 'src/Utility/Utility.php';`, where 
`'src/Utility'` is the path to where you placed Utility.

For example:

```php
<?php

include 'src/Utility/Utility.php';

use Esi\Utility\Utility;

?>
```

## Basic Usage

```php
<?php

use Esi\Utility\Utility;

?>
```

All methods of the Utility class are static. So, for example, to retrieve the information for a 
particular timezone:

```php
<?php

use Esi\Utility\Utility;

$timezone = Utility::timezoneInfo('America/New_York');

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

## About

### Requirements

- Utility works with PHP 7.0.0 or above.

### Submitting bugs and feature requests

Bugs and feature requests are tracked on [GitHub](https://github.com/ericsizemore/utility/issues)

Issues are the quickest way to report a bug. If you find a bug or documentation error, please check the following first:

* That there is not an Issue already open concerning the bug
* That the issue has not already been addressed (within closed Issues, for example)

### Contributing

Utility accepts contributions of code and documentation from the community. 
These contributions can be made in the form of Issues or [Pull Requests](http://help.github.com/send-pull-requests/) 
on the [Utility repository](https://github.com/ericsizemore/utility).

Utility is licensed under the MIT license. When submitting new features or patches to Utility, you are 
giving permission to license those features or patches under the MIT license.

#### Guidelines

Before we look into how, here are the guidelines. If your Pull Requests fail to
pass these guidelines it will be declined and you will need to re-submit when
you’ve made the changes. This might sound a bit tough, but it is required for
me to maintain quality of the code-base.

#### PHP Style

Please ensure all new contributions match the [PSR-2](http://www.php-fig.org/psr/psr-2/)
coding style guide. The project is not fully PSR-2 compatible, yet; however, to ensure 
the easiest transition to the coding guidelines, I would like to go ahead and request 
that any contributions follow them.

#### Documentation

If you change anything that requires a change to documentation then you will
need to add it. New methods, parameters, changing default values, adding
constants, etc are all things that will require a change to documentation. The
change-log must also be updated for every change. Also PHPDoc blocks must be
maintained.

#### Branching

One thing at a time: A pull request should only contain one change. That does
not mean only one commit, but one change - however many commits it took. The
reason for this is that if you change X and Y but send a pull request for both
at the same time, we might really want X but disagree with Y, meaning we cannot
merge the request. Using the Git-Flow branching model you can create new
branches for both of these features and send two requests.

### Author

Eric Sizemore - <admin@secondversion.com> - <http://www.secondversion.com>

### License

Utility is licensed under the MIT License - see the `LICENSE` file for details

### Acknowledgements

This library is heavily inspired by Brandon Wamboldt's [utilphp](http://brandonwamboldt.github.com/utilphp/)
library.
