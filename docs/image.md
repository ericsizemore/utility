# Image

`Esi\Utility\Image`

* [isGdAvailable](#isgdavailable)(): bool;
* [isGmagickAvailable](#isgmagickavailable)(): bool;
* [isImagickAvailable](#isimagickavailable)(): bool;
* [isExifAvailable](#isexifavailable)(): bool;
* [guessImageType](#guessimagetype)(string $imagePath): string | false;
* [isJpg](#isjpg)(string $imagePath): bool;
* [isGif](#isgif)(string $imagePath): bool;
* [isPng](#ispng)(string $imagePath): bool;
* [isWebp](#iswebp)(string $imagePath): bool;

#### @access private
```php
// Just helper functions for guessImageType()
guessImageTypeExif(string $imagePath): string | false;
guessImageTypeFinfo(string $imagePath): string | false;
guessImageTypeGetImageSize(string $imagePath): string | false;
```


## isGdAvailable

Check if the GD library is available on the server.

```php
use Esi\Utility\Image;

if (Image::isGdAvailable() {
    // ... run some GD related code here...
}
```

## isGmagickAvailable

Check if the GraphicsMagick library is available on the server.

```php
use Esi\Utility\Image;

if (Image::isGmagickAvailable() {
    // ... run some GD related code here...
}
```

## isImagickAvailable

Check if the ImageMagick library is available on the server.

```php
use Esi\Utility\Image;

if (Image::isImagickAvailable() {
    // ... run some GD related code here...
}
```

## isExifAvailable

Check if the Exif extension is available on the server.

```php
use Esi\Utility\Image;

if (Image::isExifAvailable() {
    // ... run some GD related code here...
}
```

## guessImageType

Attempts to determine the image type. It tries to determine the image type with, in order of preference: Exif, finfo, and getimagesize.

```php
use Esi\Utility\Image;

echo Image::guessImageType('/some/folder/image.jpg'); // 'image/jpeg'
```

## isJpg

Checks if image has JPG format.

```php
use Esi\Utility\Image;

var_dump(Image::isJpg('/some/folder/image.jpg')); // bool(true)
```

## isGif

Checks if image has GIF format.

```php
use Esi\Utility\Image;

var_dump(Image::isGif('/some/folder/image.gif')); // bool(true)
```

## isPng

Checks if image has PNG format.

```php
use Esi\Utility\Image;

var_dump(Image::isPng('/some/folder/image.png')); // bool(true)
```

## isWebp

Checks if image has WEBP format.

```php
use Esi\Utility\Image;

var_dump(Image::isWebp('/some/folder/image.webp')); // bool(true)
```
