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

use Esi\Utility\Enums\Http\StatusCodeCategories;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * StatusCodeCategories enum tests.
 */
#[CoversClass(StatusCodeCategories::class)]
class StatusCodeCategoriesTest extends TestCase
{
    /**
     * Holds all cases of the StatusCodeCategories enum.
     *
     * @var array<int, StatusCodeCategories>
     */
    protected array $categories;

    /**
     * Build the $categories array.
     */
    #[\Override]
    public function setUp(): void
    {
        $this->categories = StatusCodeCategories::cases();
    }

    /**
     * Simple, perhaps unnecessary, test to make sure no empty values.
     */
    public function testNoneEmpty(): void
    {
        self::assertNotEmpty($this->categories);

        foreach ($this->categories as $category) {
            self::assertNotEmpty($category->getValue());
        }
    }

    /**
     * Test the getName() method.
     */
    public function testGetName(): void
    {
        self::assertNotEmpty($this->categories);

        foreach ($this->categories as $category) {
            self::assertSame($category->getName(), $category->name);
        }
    }
}
