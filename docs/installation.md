# Installation

Installing Esi\Utility is very easy, if you're using [composer](http://getcomposer.com). 
If you haven't done so, install composer, and use **composer require** to install Esi\Utility.

```bash
curl -sS https://getcomposer.org/installer | php
php composer.phar require esi/utility
```

## First usage

Make sure you include `vendor/autoload.php` in your application. To make all of Utility's components available at once:

```php
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

For more information on each component:

* [\Esi\Utility\Arrays](arrays.md)
* [\Esi\Utility\Conversion](conversion.md)
* [\Esi\Utility\Dates](dates.md)
* [\Esi\Utility\Environment](environment.md)
* [\Esi\Utility\Filesystem](filesystem.md)
* [\Esi\Utility\Image](image.md)
* [\Esi\Utility\Numbers](numbers.md)
* [\Esi\Utility\Strings](strings.md)
