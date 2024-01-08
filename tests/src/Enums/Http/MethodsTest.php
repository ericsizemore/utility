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

use Esi\Utility\Enums\Http\Methods;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Methods enum tests.
 */
#[CoversClass(Methods::class)]
class MethodsTest extends TestCase
{
    /**
     * Holds all cases of the Methods enum.
     *
     * @var array<int, Methods>
     */
    protected array $methods;

    /**
     * Build the $methods array.
     */
    #[\Override]
    public function setUp(): void
    {
        $this->methods = Methods::cases();
    }

    /**
     * Simple, perhaps unnecessary, test to make sure no empty values.
     */
    public function testNoneEmpty(): void
    {
        self::assertNotEmpty($this->methods);

        foreach ($this->methods as $method) {
            self::assertNotEmpty($method->getValue());
        }
    }

    /**
     * Test the isSafe() method.
     */
    public function testIsSafe(): void
    {
        $safe = [
            Methods::GET->getValue(),
            Methods::HEAD->getValue(),
            Methods::OPTIONS->getValue(),
            Methods::PRI->getValue(),
            Methods::PROPFIND->getValue(),
            Methods::REPORT->getValue(),
            Methods::SEARCH->getValue(),
            Methods::TRACE->getValue(),
        ];

        foreach ($this->methods as $method) {
            if (in_array($method->getName(), $safe, true)) {
                self::assertTrue($method->isSafe());
            } else {
                self::assertFalse($method->isSafe());
            }
        }
    }

    /**
     * Test the isIdempotent() method.
     */
    public function testIsIdempotent(): void
    {
        $notIdempotent = [
            Methods::CONNECT->getValue(),
            Methods::LOCK->getValue(),
            Methods::PATCH->getValue(),
            Methods::POST->getValue(),
        ];

        foreach ($this->methods as $method) {
            if (in_array($method->getName(), $notIdempotent, true)) {
                self::assertFalse($method->isIdempotent());
            } else {
                self::assertTrue($method->isIdempotent());
            }
        }
    }
}
