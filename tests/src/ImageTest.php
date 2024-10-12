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

namespace Esi\Utility\Tests;

use Esi\Utility\Arrays;
use Esi\Utility\Filesystem;
use Esi\Utility\Image;
use Esi\Utility\Strings;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use const DIRECTORY_SEPARATOR;

/**
 * Image utility test.
 *
 * @internal
 */
#[CoversClass(Image::class)]
#[CoversMethod(Arrays::class, 'valueExists')]
#[CoversMethod(Filesystem::class, 'isFile')]
#[CoversMethod(Strings::class, 'beginsWith')]
class ImageTest extends TestCase
{
    protected string $resourceDir;

    /**
     * @var array<string>
     */
    protected array $resources;

    #[\Override]
    protected function setUp(): void
    {
        $this->resourceDir = \dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'resources/';

        $this->resources = [
            'image/jpeg' => $this->resourceDir . 'testImage.jpg',
            'image/png'  => $this->resourceDir . 'testImage.png',
            'image/gif'  => $this->resourceDir . 'testImage.gif',
            'image/webp' => $this->resourceDir . 'testImage.webp',
        ];
    }

    public function testGuessImageType(): void
    {
        foreach ($this->resources as $key => $val) {
            $result = Image::guessImageType($val);

            self::assertIsString($result);
            self::assertSame($key, $result);

        }
    }

    #[RequiresPhpExtension('fileinfo')]
    public function testGuessImageTypeFinfo(): void
    {
        $this->testGuessImageType();
    }

    public function testGuessImageTypeInvalidFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::guessImageType('');
    }

    #[RequiresPhpExtension('exif')]
    public function testIsExifAvailable(): void
    {
        self::assertTrue(Image::isExifAvailable());
    }

    #[RequiresPhpExtension('gd')]
    public function testIsGdAvailable(): void
    {
        self::assertTrue(Image::isGdAvailable());
    }

    public function testIsGif(): void
    {
        self::assertTrue(Image::isGif($this->resources['image/gif']));
    }

    public function testIsGifInvalidFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::isGif($this->resourceDir . 'doesNotExist.gif');
    }

    public function testIsGifInvalidImageFile(): void
    {
        $this->expectException(RuntimeException::class);
        Image::isGif($this->resourceDir . 'notAnImage.txt');
    }

    #[RequiresPhpExtension('gmagick')]
    public function testIsGmagickAvailable(): void
    {
        self::assertTrue(Image::isGmagickAvailable());
    }

    #[RequiresPhpExtension('imagick')]
    public function testIsImagickAvailable(): void
    {
        self::assertTrue(Image::isImagickAvailable());
    }

    public function testIsJpg(): void
    {
        self::assertTrue(Image::isJpg($this->resources['image/jpeg']));
    }

    public function testIsJpgInvalidFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::isJpg($this->resourceDir . 'doesNotExist.jpg');
    }

    public function testIsJpgInvalidImageFile(): void
    {
        $this->expectException(RuntimeException::class);
        Image::isJpg($this->resourceDir . 'notAnImage.txt');
    }

    public function testIsPng(): void
    {
        self::assertTrue(Image::isPng($this->resources['image/png']));
    }

    public function testIsPngInvalidFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::isPng($this->resourceDir . 'doesNotExist.png');
    }

    public function testIsPngInvalidImageFile(): void
    {
        $this->expectException(RuntimeException::class);
        Image::isPng($this->resourceDir . 'notAnImage.txt');
    }

    public function testIsWebp(): void
    {
        self::assertTrue(Image::isWebp($this->resources['image/webp']));
    }

    public function testIsWebpInvalidFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::isWebp($this->resourceDir . 'doesNotExist.webp');
    }

    public function testIsWebpInvalidImageFile(): void
    {
        $this->expectException(RuntimeException::class);
        Image::isWebp($this->resourceDir . 'notAnImage.txt');
    }
}
