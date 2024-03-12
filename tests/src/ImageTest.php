<?php

declare(strict_types=1);

/**
 * Utility - Collection of various PHP utility functions.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 *
 * @version   2.0.0
 *
 * @copyright (C) 2017 - 2024 Eric Sizemore
 * @license   The MIT License (MIT)
 *
 * Copyright (C) 2017 - 2024 Eric Sizemore <https://www.secondversion.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Esi\Utility\Tests;

use Esi\Utility\Image;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Image utility test.
 *
 * @internal
 */
#[CoversClass(Image::class)]
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
        $this->resourceDir = dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'resources/';

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

    public function testGuessImageTypeInvalidFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::guessImageType('');
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

    public function testIsPng(): void
    {
        self::assertTrue(Image::isPng($this->resources['image/png']));
    }

    public function testIsPngInvalidImageFile(): void
    {
        $this->expectException(RuntimeException::class);
        Image::isPng($this->resourceDir . 'notAnImage.txt');
    }

    public function testIsPngInvalidFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::isPng($this->resourceDir . 'doesNotExist.png');
    }

    public function testIsWebp(): void
    {
        self::assertTrue(Image::isWebp($this->resources['image/webp']));
    }

    public function testIsWebpInvalidImageFile(): void
    {
        $this->expectException(RuntimeException::class);
        Image::isWebp($this->resourceDir . 'notAnImage.txt');
    }

    public function testIsWebpInvalidFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Image::isWebp($this->resourceDir . 'doesNotExist.webp');
    }
}
