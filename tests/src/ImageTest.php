<?php

declare(strict_types=1);

/**
 * This file is part of Esi\Utility.
 *
 * (c) 2017 - 2025 Eric Sizemore <admin@secondversion.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 */

namespace Esi\Utility\Tests;

use Esi\Utility\Arrays;
use Esi\Utility\Filesystem;
use Esi\Utility\Image;
use Esi\Utility\Strings;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use const DIRECTORY_SEPARATOR;

/**
 * Image utility test.
 *
 * @internal
 *
 * @psalm-api
 */
#[CoversClass(Image::class)]
#[CoversMethod(Arrays::class, 'valueExists')]
#[CoversMethod(Filesystem::class, 'isFile')]
#[CoversMethod(Strings::class, 'beginsWith')]
final class ImageTest extends TestCase
{
    private string $resourceDir;

    /**
     * @var array<string>
     */
    private array $resources;

    #[\Override]
    protected function setUp(): void
    {
        $this->resourceDir = \sprintf('%s%sresources/', \dirname(__FILE__, 2), DIRECTORY_SEPARATOR);

        $this->resources = [
            'image/jpeg' => \sprintf('%stestImage.jpg', $this->resourceDir),
            'image/png'  => \sprintf('%stestImage.png', $this->resourceDir),
            'image/gif'  => \sprintf('%stestImage.gif', $this->resourceDir),
            'image/webp' => \sprintf('%stestImage.webp', $this->resourceDir),
        ];
    }

    #[RequiresPhpExtension('fileinfo')]
    #[Test]
    public function guessImageTypeFinfo(): void
    {
        $this->guessImageTypeGuessesCorrectly();
    }

    #[Test]
    public function guessImageTypeGuessesCorrectly(): void
    {
        foreach ($this->resources as $key => $val) {
            $result = Image::guessImageType($val);

            self::assertIsString($result);
            self::assertSame($key, $result);
        }
    }

    #[Test]
    public function guessImageTypeInvalidFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::guessImageType('');
    }

    #[Test]
    public function imageIsGif(): void
    {
        self::assertTrue(Image::isGif($this->resources['image/gif']));
    }

    #[Test]
    public function imageIsJpg(): void
    {
        self::assertTrue(Image::isJpg($this->resources['image/jpeg']));
    }

    #[Test]
    public function imageIsPng(): void
    {
        self::assertTrue(Image::isPng($this->resources['image/png']));
    }

    #[Test]
    public function imageIsWebp(): void
    {
        self::assertTrue(Image::isWebp($this->resources['image/webp']));
    }

    #[RequiresPhpExtension('exif')]
    #[Test]
    public function isExifAvailable(): void
    {
        self::assertTrue(Image::isExifAvailable());
    }

    #[RequiresPhpExtension('gd')]
    #[Test]
    public function isGdAvailable(): void
    {
        self::assertTrue(Image::isGdAvailable());
    }

    #[Test]
    public function isGifThrowsExceptionForInvalidImageFile(): void
    {
        $this->expectException(RuntimeException::class);
        Image::isGif($this->resourceDir . 'notAnImage.txt');
    }

    #[Test]
    public function isGifThrowsExceptionForNonExistentFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::isGif($this->resourceDir . 'doesNotExist.gif');
    }

    #[RequiresPhpExtension('gmagick')]
    #[Test]
    public function isGmagickAvailable(): void
    {
        self::assertTrue(Image::isGmagickAvailable());
    }

    #[Test]
    #[RequiresPhpExtension('imagick')]
    public function isImagickAvailable(): void
    {
        self::assertTrue(Image::isImagickAvailable());
    }

    #[Test]
    public function isJpgThrowsExceptionForInvalidImageFile(): void
    {
        $this->expectException(RuntimeException::class);
        Image::isJpg($this->resourceDir . 'notAnImage.txt');
    }

    #[Test]
    public function isJpgThrowsExceptionForNonExistentFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::isJpg($this->resourceDir . 'doesNotExist.jpg');
    }

    #[Test]
    public function isPngThrowsExceptionForInvalidImageFile(): void
    {
        $this->expectException(RuntimeException::class);
        Image::isPng($this->resourceDir . 'notAnImage.txt');
    }

    #[Test]
    public function isPngThrowsExceptionForNonExistentFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::isPng($this->resourceDir . 'doesNotExist.png');
    }

    #[Test]
    public function isWebpThrowsExceptionForInvalidImageFile(): void
    {
        $this->expectException(RuntimeException::class);
        Image::isWebp($this->resourceDir . 'notAnImage.txt');
    }

    #[Test]
    public function testIsWebpThrowsExceptionForNonExistentFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::isWebp($this->resourceDir . 'doesNotExist.webp');
    }
}
