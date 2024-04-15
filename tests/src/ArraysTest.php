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

use ArrayAccess;
use Esi\Utility\Arrays;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Array utilities tests.
 *
 * @internal
 */
#[CoversClass(Arrays::class)]
class ArraysTest extends TestCase
{
    public function testExistsDeprecation(): void
    {
        $array = ['test' => 1];

        $testArrayAccess         = new TestArrayAccess();
        $testArrayAccess['test'] = 1;

        $this->expectUserDeprecationMessage(Arrays::class . '::exists is deprecated and will be removed in v2.1.0, use ' . Arrays::class . '::keyExists instead.');
        self::assertTrue(Arrays::exists($array, 'test'));
        self::assertFalse(Arrays::exists($array, 'this'));

        self::assertTrue(Arrays::exists($testArrayAccess, 'test'));
        self::assertFalse(Arrays::exists($testArrayAccess, 'this'));
    }

    /**
     * Test Arrays::flatten().
     */
    public function testFlatten(): void
    {
        self::assertSame([
            0           => 'a',
            1           => 'b',
            2           => 'c',
            3           => 'd',
            '4.first'   => 'e',
            '4.0'       => 'f',
            '4.second'  => 'g',
            '4.1.0'     => 'h',
            '4.1.third' => 'i',
        ], Arrays::flatten([
            'a', 'b', 'c', 'd', ['first' => 'e', 'f', 'second' => 'g', ['h', 'third' => 'i']],
        ]));

        self::assertSame(
            [0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd', '4.0' => 'e', '4.1' => 'f', '4.2' => 'g'],
            Arrays::flatten(['a', 'b', 'c', 'd', ['e', 'f', 'g']])
        );

        self::assertSame(
            ['k0' => 'a', 'k1' => 'b', 'k2' => 'c', 'k3' => 'd', 'k4.0' => 'e', 'k4.1' => 'f', 'k4.2' => 'g'],
            Arrays::flatten(['a', 'b', 'c', 'd', ['e', 'f', 'g']], '.', 'k')
        );
    }

    /**
     * Test Arrays::get().
     */
    public function testGet(): void
    {
        $array = ['this' => 'is', 'a' => 'test'];

        self::assertSame('is', Arrays::get($array, 'this'));
        self::assertSame('test', Arrays::get($array, 'a'));
        self::assertNull(Arrays::get($array, 'notexist'));
    }

    /**
     * Test Arrays::groupBy().
     */
    public function testGroupBy(): void
    {
        $result = Arrays::groupBy([
            ['id' => 1, 'category' => 'A', 'value' => 'foo'],
            ['id' => 2, 'category' => 'B', 'value' => 'bar'],
            ['id' => 3, 'category' => 'A', 'value' => 'baz'],
            ['id' => 4, 'category' => 'B', 'value' => 'qux'],
        ], 'category');

        $expected = [
            'A' => [
                ['id' => 1, 'category' => 'A', 'value' => 'foo'],
                ['id' => 3, 'category' => 'A', 'value' => 'baz'],
            ],
            'B' => [
                ['id' => 2, 'category' => 'B', 'value' => 'bar'],
                ['id' => 4, 'category' => 'B', 'value' => 'qux'],
            ],
        ];

        self::assertSame($expected, $result);
    }

    /**
     * Test Arrays::groupBy() with non-existent/invalid key.
     */
    public function testGroupByInvalidKey(): void
    {
        $result = Arrays::groupBy([
            ['id' => 1, 'category' => 'A', 'value' => 'foo'],
            ['id' => 2, 'category' => 'B', 'value' => 'bar'],
            ['id' => 3, 'category' => 'A', 'value' => 'baz'],
            ['id' => 4, 'category' => 'B', 'value' => 'qux'],
        ], 'notakey');

        $expected = [];

        self::assertSame($expected, $result);
    }

    /**
     * Test Arrays::interlace().
     */
    public function testInterlace(): void
    {
        $input  = Arrays::interlace([1, 2, 3], ['a', 'b', 'c']);
        $expect = [1, 'a', 2, 'b', 3, 'c'];

        self::assertSame($expect, $input);

        // With one argument
        self::assertSame([1, 2, 3], Arrays::interlace([1, 2, 3]));

        // With no arguments
        self::assertFalse(Arrays::interlace());
    }

    /**
     * Test Arrays::isAssociative().
     */
    public function testIsAssociative(): void
    {
        $array    = [0, 1, 2, 3, 4];
        $arrayTwo = ['test' => 'testing', 'testing' => 'what'];

        self::assertFalse(Arrays::isAssociative($array));
        self::assertTrue(Arrays::isAssociative($arrayTwo));
    }

    /**
     * Test Arrays::keyExists().
     */
    public function testKeyExists(): void
    {
        $array = ['test' => 1];

        $testArrayAccess         = new TestArrayAccess();
        $testArrayAccess['test'] = 1;

        self::assertTrue(Arrays::keyExists($array, 'test'));
        self::assertFalse(Arrays::keyExists($array, 'this'));

        self::assertTrue(Arrays::keyExists($testArrayAccess, 'test'));
        self::assertFalse(Arrays::keyExists($testArrayAccess, 'this'));
    }

    /**
     * Test Arrays::mapDeep().
     */
    public function testMapDeep(): void
    {
        self::assertSame([
            '&lt;',
            'abc',
            '&gt;',
            'def',
            ['&amp;', 'test', '123'],
        ], Arrays::mapDeep([
            '<',
            'abc',
            '>',
            'def',
            ['&', 'test', '123'],
        ], 'htmlentities'));

        $var       = new stdClass();
        $var->test = ['test' => '>'];
        $var->what = '<';

        $var2       = new stdClass();
        $var2->test = ['test' => '&gt;'];
        $var2->what = '&lt;';

        self::assertEquals($var2, Arrays::mapDeep($var, 'htmlentities'));
    }

    /**
     * Test Arrays::set().
     */
    public function testSet(): void
    {
        $array    = ['this' => 1, 'is' => 2, 'a' => 3, 'test' => 4];
        $newArray = ['that' => 4, 'was' => 3, 'a' => 2, 'test' => 1];

        Arrays::set($array, 'test', 5);
        self::assertSame(5, Arrays::get($array, 'test'));

        Arrays::set($array, null, $newArray);
        self::assertSame(4, Arrays::get($array, 'that'));
    }

    /**
     * Test Arrays::valueExists().
     */
    public function testValueExists(): void
    {
        $array = ['test' => 1, 1 => 'foo', 'bar' => 2];

        self::assertTrue(Arrays::valueExists($array, 1));
        self::assertFalse(Arrays::valueExists($array, 'test'));

        self::assertTrue(Arrays::valueExists($array, 'foo'));
        self::assertFalse(Arrays::valueExists($array, 'bar'));
    }
}

/**
 * @implements ArrayAccess<mixed, mixed>
 */
class TestArrayAccess implements ArrayAccess
{
    /**
     * @var array<int|string, mixed>
     */
    public array $container = [
        'one'   => 1,
        'two'   => 2,
        'three' => 3,
    ];

    /**
     * Whether an offset exists.
     */
    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Retrieve an offset exists.
     */
    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Set an offset.
     */
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (\is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unset an offset.
     */
    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        unset($this->container[$offset]);
    }
}
