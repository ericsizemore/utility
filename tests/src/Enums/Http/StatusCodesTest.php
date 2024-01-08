<?php

declare(strict_types=1);

/**
 * Utility - Collection of various PHP utility functions.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @version   2.0.0
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

namespace Esi\Utility\Tests\Enums\Http;

use Esi\Utility\Enums\Http\StatusCodes;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * StatusCodes enum tests.
 */
#[CoversClass(StatusCodes::class)]
class StatusCodesTest extends TestCase
{
    /**
     * Holds all cases of the StatusCodes enum.
     *
     * @var array<int, StatusCodes>
     */
    protected array $codes;

    /**
     * Build the $codes array.
     */
    #[\Override]
    public function setUp(): void
    {
        $this->codes = StatusCodes::cases();
    }

    /**
     * Simple, perhaps unnecessary, test to make sure no empty values and all returned values are type int.
     */
    public function testNoneEmptyIsInt(): void
    {
        self::assertNotEmpty($this->codes);

        foreach ($this->codes as $code) {
            self::assertNotEmpty($code->getValue());
            self::assertIsInt($code->getValue());
        }
    }

    /**
     * Simple, perhaps unnecessary, test to make sure all returned values are within range of 100, 511.
     */
    public function testWithinRange(): void
    {
        self::assertNotEmpty($this->codes);

        foreach ($this->codes as $code) {
            self::assertGreaterThanOrEqual(100, $code->getValue());
            self::assertLessThanOrEqual(511, $code->getValue());
        }
    }

    /**
     * Test the getMessage() method.
     */
    public function testGetMessage(): void
    {
        self::assertNotEmpty($this->codes);

        foreach ($this->codes as $code) {
            self::assertNotEmpty($code->getMessage());
            self::assertIsString($code->getMessage());
            self::assertSame(match ($code->getName()) {
                'Non_Authoritative_Information' => 'Non-authoritative Information',
                'Multi_Status' => 'Multi-Status',
                'Im_A_Teapot' => "I'm A Teapot",
                'Request_URI_Too_Long' => 'Request-URI Too Long',
                default => str_replace('_', ' ', $code->getName()),
            }, $code->getMessage());
        }
    }

    /**
     * Test the getCategory() method.
     */
    public function testGetCategory(): void
    {
        self::assertNotEmpty($this->codes);

        foreach ($this->codes as $code) {
            self::assertSame(str_replace(' ', '', $code->getCategory()->getValue()), $code->getCategory()->getName());
        }
    }
}
