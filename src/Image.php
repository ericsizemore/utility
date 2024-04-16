<?php

declare(strict_types=1);

/**
 * This file is part of Esi\Utility.
 *
 * (c) 2017 - 2024 Eric Sizemore <admin@secondversion.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 */

namespace Esi\Utility;

use InvalidArgumentException;
use RuntimeException;

use function class_exists;
use function explode;
use function getimagesize;
use function image_type_to_mime_type;

/**
 * Image utilities.
 *
 * @since 2.0.0
 * @see Tests\ImageTest
 */
final class Image
{
    /**
     * Image type/mime strings to determine image type.
     *
     * @var array<string, array<string>> IMAGE_TYPES
     */
    public const IMAGE_TYPES = [
        'jpg'  => ['image/jpg', 'image/jpeg'],
        'gif'  => ['image/gif'],
        'png'  => ['image/png'],
        'webp' => ['image/webp'],
    ];

    /**
     * Attempts to determine the image type. It tries to determine the image type with, in order
     * of preference: Exif, finfo, and getimagesize.
     *
     * @param string $imagePath File path to the image.
     *
     * @return string|false Returns the image type string on success, false on any failure.
     */
    public static function guessImageType(string $imagePath): string | false
    {
        static $hasFinfo;

        if (!Filesystem::isFile($imagePath)) {
            throw new InvalidArgumentException('$imagePath not found or is not a file.');
        }

        // If Exif is available, let's start there. It's the fastest.
        //@codeCoverageIgnoreStart
        if (self::isExifAvailable()) {
            return self::guessImageTypeExif($imagePath);
        }

        $hasFinfo ??= class_exists('finfo');

        if ($hasFinfo) {
            // Next, let's try finfo
            return self::guessImageTypeFinfo($imagePath);
        }

        // Last resort: getimagesize can be pretty slow, especially compared to exif_imagetype
        // It may not return "mime". Theoretically, this should only happen for a file that is not an image.
        return self::guessImageTypeGetImageSize($imagePath);
        //@codeCoverageIgnoreEnd
    }

    /**
     * Check if the Exif extension is available on the server.
     */
    public static function isExifAvailable(): bool
    {
        //@codeCoverageIgnoreStart
        static $hasExif;

        $hasExif ??= \extension_loaded('exif');

        return $hasExif;
        //@codeCoverageIgnoreEnd
    }

    /**
     * Check if the GD library is available on the server.
     */
    public static function isGdAvailable(): bool
    {
        //@codeCoverageIgnoreStart
        static $hasGd;

        $hasGd ??= \extension_loaded('gd');

        return $hasGd;
        //@codeCoverageIgnoreEnd
    }

    /**
     * Checks if image has GIF format.
     *
     * @param string $imagePath File path to the image.
     *
     * @throws InvalidArgumentException If the image path provided is not valid.
     * @throws RuntimeException         If we are unable to determine the file type.
     */
    public static function isGif(string $imagePath): bool
    {
        $imageType = self::guessImageType($imagePath);

        if ($imageType === false) {
            throw new RuntimeException('Unable to determine the image type. Is it a valid image file?');
        }

        return Arrays::valueExists(self::IMAGE_TYPES['gif'], $imageType);
    }

    /**
     * Check if the GraphicsMagick library is available on the server.
     */
    public static function isGmagickAvailable(): bool
    {
        //@codeCoverageIgnoreStart
        static $hasGmagick;

        $hasGmagick ??= \extension_loaded('gmagick');

        return $hasGmagick;
        //@codeCoverageIgnoreEnd
    }

    /**
     * Check if the ImageMagick library is available on the server.
     */
    public static function isImagickAvailable(): bool
    {
        //@codeCoverageIgnoreStart
        static $hasImagick;

        $hasImagick ??= \extension_loaded('imagick');

        return $hasImagick;
        //@codeCoverageIgnoreEnd
    }

    /**
     * Checks if image has JPG format.
     *
     * @param string $imagePath File path to the image.
     *
     * @throws InvalidArgumentException If the image path provided is not valid.
     * @throws RuntimeException         If we are unable to determine the file type.
     */
    public static function isJpg(string $imagePath): bool
    {
        $imageType = self::guessImageType($imagePath);

        if ($imageType === false) {
            throw new RuntimeException('Unable to determine the image type. Is it a valid image file?');
        }

        return Arrays::valueExists(self::IMAGE_TYPES['jpg'], $imageType);
    }

    /**
     * Checks if image has PNG format.
     *
     * @param string $imagePath File path to the image.
     *
     * @throws InvalidArgumentException If the image path provided is not valid.
     * @throws RuntimeException         If we are unable to determine the file type.
     */
    public static function isPng(string $imagePath): bool
    {
        $imageType = self::guessImageType($imagePath);

        if ($imageType === false) {
            throw new RuntimeException('Unable to determine the image type. Is it a valid image file?');
        }

        return Arrays::valueExists(self::IMAGE_TYPES['png'], $imageType);
    }

    /**
     * Checks if image has WEBP format.
     *
     * @param string $imagePath File path to the image.
     *
     * @throws InvalidArgumentException If the image path provided is not valid.
     * @throws RuntimeException         If we are unable to determine the file type.
     */
    public static function isWebp(string $imagePath): bool
    {
        $imageType = self::guessImageType($imagePath);

        if ($imageType === false) {
            throw new RuntimeException('Unable to determine the image type. Is it a valid image file?');
        }

        return Arrays::valueExists(self::IMAGE_TYPES['webp'], $imageType);
    }

    /**
     * Helper function for guessImageType().
     *
     * If the Exif extension is available, use Exif to determine mime type.
     *
     * @param string $imagePath File path to the image.
     *
     * @return string|false Returns the image type string on success, false on any failure.
     */
    private static function guessImageTypeExif(string $imagePath): string | false
    {
        //@codeCoverageIgnoreStart
        // Ignoring code coverage as if one method is available over another, the others won't be or need to be tested
        $imageType = @\exif_imagetype($imagePath);

        return (\is_int($imageType) ? image_type_to_mime_type($imageType) : false);
        //@codeCoverageIgnoreEnd
    }

    /**
     * Helper function for guessImageType().
     *
     * If the FileInfo (finfo) extension is available, use finfo to determine mime type.
     *
     * @param string $imagePath File path to the image.
     *
     * @return string|false Returns the image type string on success, false on any failure.
     */
    private static function guessImageTypeFinfo(string $imagePath): string | false
    {
        //@codeCoverageIgnoreStart
        // Ignoring code coverage as if one method is available over another, the others won't be or need to be tested
        $finfo  = new \finfo(\FILEINFO_MIME);
        $result = $finfo->file($imagePath);

        if ($result === false) {
            // false means an error occurred
            return false;
        }

        [$mime, ] = explode('; ', $result);

        if (Strings::beginsWith($mime, 'image/')) {
            return $mime;
        }

        return false;
        //@codeCoverageIgnoreEnd
    }

    /**
     * Helper function for guessImageType().
     *
     * If the Exif extension is available, use Exif to determine mime type.
     *
     * @param string $imagePath File path to the image.
     *
     * @return string|false Returns the image type string on success, false on any failure.
     */
    private static function guessImageTypeGetImageSize(string $imagePath): string | false
    {
        //@codeCoverageIgnoreStart
        // Ignoring code coverage as if one method is available over another, the others won't be or need to be tested
        $imageSize = @getimagesize($imagePath);

        return $imageSize['mime'] ?? false;
        //@codeCoverageIgnoreEnd
    }
}
