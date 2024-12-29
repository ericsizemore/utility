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

use ArrayObject;
use Esi\Utility\Arrays;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Tests for the Arrays utility class.
 *
 * @psalm-type ArrayInput = array<array-key, mixed>
 * @psalm-type GroupTestItem = array{type?: string, name: string}
 * @psalm-type GroupTestArray = array<array-key, GroupTestItem>
 * @psalm-type GroupTestResult = array<array-key, non-empty-list<GroupTestItem>>
 *
 * @final
 *
 * @internal
 */
#[CoversClass(Arrays::class)]
final class ArraysTest extends TestCase
{
    /**
     * Tests the flatten method with an empty array.
     */
    #[Test]
    public function flattenShouldHandleEmptyArray(): void
    {
        $result = Arrays::flatten([]);

        self::assertSame([], $result);
    }

    /**
     * Tests the flatten method with nested arrays.
     */
    #[Test]
    public function flattenShouldReturnFlattenedArray(): void
    {
        /** @var array<array-key, mixed> $input */
        $input = [
            'a' => 1,
            'b' => [
                'c' => 2,
                'd' => [
                    'e' => 3,
                ],
            ],
        ];

        /** @var array<string, int> $expected */
        $expected = [
            'a'     => 1,
            'b.c'   => 2,
            'b.d.e' => 3,
        ];

        $result = Arrays::flatten($input);

        self::assertSame($expected, $result);
    }

    /**
     * Tests the flatten method with a custom separator.
     */
    #[Test]
    public function flattenShouldWorkWithCustomSeparator(): void
    {
        /** @var array<array-key, mixed> $input */
        $input = [
            'a' => 1,
            'b' => [
                'c' => 2,
            ],
        ];

        /** @var array<string, int> $expected */
        $expected = [
            'a'   => 1,
            'b/c' => 2,
        ];

        $result = Arrays::flatten($input, '/');

        self::assertSame($expected, $result);
    }

    /**
     * Tests the get method with various inputs.
     *
     * @param array<string, string>|ArrayObject<string, string> $array
     */
    #[Test]
    #[DataProvider('getDataProvider')]
    public function getShouldReturnCorrectValue(array|ArrayObject $array, string $key, mixed $default, mixed $expected): void
    {
        $result = Arrays::get($array, $key, $default);

        self::assertSame($expected, $result);
    }

    /**
     * Tests the groupBy method.
     *
     * @param GroupTestArray   $input
     * @param non-empty-string $key
     * @param GroupTestResult  $expected
     */
    #[Test]
    #[DataProvider('groupByDataProvider')]
    public function groupByShouldGroupArrayCorrectly(array $input, string $key, array $expected): void
    {
        $result = Arrays::groupBy($input, $key);

        self::assertSame($expected, $result);
    }

    /**
     * Tests the interlace method.
     *
     * @param array<int, array<int, int|string>> $arrays
     * @param array<int, int|string>|false       $expected
     */
    #[Test]
    #[DataProvider('interlaceDataProvider')]
    public function interlaceShouldWorkCorrectly(array $arrays, array|false $expected): void
    {
        $result = Arrays::interlace(...$arrays);

        self::assertSame($expected, $result);
    }

    /**
     * Tests the isAssociative method.
     *
     * @param array<array-key, mixed> $array
     */
    #[Test]
    #[DataProvider('isAssociativeDataProvider')]
    public function isAssociativeShouldDetectCorrectly(array $array, bool $expected): void
    {
        $result = Arrays::isAssociative($array);

        self::assertSame($expected, $result);
    }

    /**
     * Tests keyExists with various scenarios.
     */
    #[Test]
    public function keyExistsShouldCheckCorrectly(): void
    {
        // Test with regular array
        $array = ['key' => 'value'];
        self::assertTrue(Arrays::keyExists($array, 'key'));
        self::assertFalse(Arrays::keyExists($array, 'nonexistent'));

        // Test with ArrayAccess
        $arrayObject = new ArrayObject(['key' => 'value']);
        self::assertTrue(Arrays::keyExists($arrayObject, 'key'));
        self::assertFalse(Arrays::keyExists($arrayObject, 'nonexistent'));

        // Test with numeric keys
        $numericArray = [0 => 'zero', 1 => 'one'];
        self::assertTrue(Arrays::keyExists($numericArray, 0));
        self::assertFalse(Arrays::keyExists($numericArray, 2));
    }

    /**
     * Tests the mapDeep method with circular references.
     */
    #[Test]
    public function mapDeepShouldHandleCircularReferences(): void
    {
        $obj1      = new stdClass();
        $obj2      = new stdClass();
        $obj1->ref = $obj2;
        $obj2->ref = $obj1;

        $obj1->value = 'test';

        $callback = static fn ($value): string => \is_string($value) ? strtoupper($value) : (string) $value;

        /** @var stdClass $result */
        $result = Arrays::mapDeep($obj1, $callback);

        self::assertSame('TEST', $result->value);
    }

    /**
     * Tests mapDeep with circular references.
     */
    #[Test]
    public function mapDeepShouldHandleCircularReferencesCorrectly(): void
    {
        $obj1      = new stdClass();
        $obj2      = new stdClass();
        $obj1->ref = $obj2;
        $obj2->ref = $obj1;

        $obj1->value = 'test';
        $obj2->value = 'test2';

        $callback = static fn (mixed $value): string => \is_string($value) ? strtoupper($value) : (string) $value;

        /** @var stdClass $result */
        $result = Arrays::mapDeep($obj1, $callback);

        /** @var stdClass $ref */
        $ref = $result->ref;

        self::assertIsString($result->value);
        self::assertIsString($ref->value);
        self::assertSame('TEST', $result->value);
        self::assertSame('TEST2', $ref->value);
        self::assertSame($result, $ref->ref);
    }

    /**
     * Tests the mapDeep method with complex nested structures.
     */
    #[Test]
    public function mapDeepShouldHandleComplexStructures(): void
    {
        $nested        = new stdClass();
        $nested->value = 'nested';

        $obj         = new stdClass();
        $obj->name   = 'test';
        $obj->nested = $nested;

        /** @var array{
         *     string: string,
         *     array: array<int>,
         *     object: stdClass,
         *     nested: array{a: array{b: string}}
         * } $input */
        $input = [
            'string' => 'hello',
            'array'  => [1, 2, 3],
            'object' => $obj,
            'nested' => ['a' => ['b' => 'c']],
        ];

        $callback = static fn (mixed $value): string => \is_string($value) ? strtoupper($value) : (string) $value;

        /** @var array{
         *     string: string,
         *     array: array<string>,
         *     object: stdClass,
         *     nested: array{a: array{b: string}}
         * } $result */
        $result = Arrays::mapDeep($input, $callback);

        self::assertSame('HELLO', $result['string']);
        self::assertSame(['1', '2', '3'], $result['array']);

        /** @var stdClass $resultObj */
        $resultObj = $result['object'];
        self::assertSame('TEST', $resultObj->name);
        self::assertInstanceOf(stdClass::class, $resultObj->nested);
        self::assertSame('NESTED', $resultObj->nested->value);

        self::assertSame(['a' => ['b' => 'C']], $result['nested']);
    }

    /**
     * Tests mapDeep with complex nested structures.
     *
     * @param array<array-key, mixed> $input    The input structure to test
     * @param callable(mixed): string $callback The callback to apply
     * @param array<array-key, mixed> $expected The expected result
     */
    #[Test]
    #[DataProvider('complexStructuresDataProvider')]
    public function mapDeepShouldHandleComplexStructuresCorrectly(array $input, callable $callback, array $expected): void
    {
        $result = Arrays::mapDeep($input, $callback);

        self::assertEquals($expected, $result);
    }

    /**
     * Tests mapDeep with empty structures.
     */
    #[Test]
    public function mapDeepShouldHandleEmptyStructures(): void
    {
        $callback = static fn ($value): string => \is_string($value) ? strtoupper($value) : (string) $value;

        self::assertSame([], Arrays::mapDeep([], $callback));
        self::assertEquals(new stdClass(), Arrays::mapDeep(new stdClass(), $callback));
    }

    /**
     * Tests mapDeep with primitive values.
     */
    #[Test]
    public function mapDeepShouldHandlePrimitiveValues(): void
    {
        $callback = static fn (mixed $value): string => \is_string($value) ? strtoupper($value) : (string) $value;

        self::assertSame('42', Arrays::mapDeep(42, $callback));
        self::assertSame('HELLO', Arrays::mapDeep('hello', $callback));
        self::assertSame('1', Arrays::mapDeep(true, $callback));
    }

    /**
     * Tests the set method.
     *
     * @param array<array-key, mixed>|ArrayObject<array-key, mixed> $array
     */
    #[Test]
    #[DataProvider('setDataProvider')]
    public function setShouldSetValueCorrectly(array|ArrayObject $array, ?string $key, mixed $value, mixed $expected): void
    {
        Arrays::set($array, $key, $value);

        if ($array instanceof ArrayObject) {
            self::assertSame($expected, $array->getArrayCopy());
        } else {
            self::assertSame($expected, $array);
        }
    }

    /**
     * Tests the valueExists method with various scenarios.
     *
     * @param array<array-key, mixed> $array
     */
    #[Test]
    #[DataProvider('valueExistsDataProvider')]
    public function valueExistsShouldCheckCorrectly(array $array, mixed $value, bool $expected): void
    {
        $result = Arrays::valueExists($array, $value);

        self::assertSame($expected, $result);
    }

    /**
     * Provides test cases for complex nested structures.
     *
     * @return Generator<string, array{
     *     input: array<string, mixed>,
     *     callback: callable(mixed): string,
     *     expected: array<string, mixed>
     * }>
     */
    public static function complexStructuresDataProvider(): Generator
    {
        $callback = static fn (mixed $value): string => \is_string($value) ? strtoupper($value) : (string) $value;

        $obj       = new stdClass();
        $obj->name = 'test';

        yield 'nested arrays and objects' => [
            'input' => [
                'string' => 'hello',
                'array'  => [1, 2, 3],
                'nested' => [
                    'object' => $obj,
                    'deep'   => ['a' => ['b' => 'c']],
                ],
            ],
            'callback' => $callback,
            'expected' => [
                'string' => 'HELLO',
                'array'  => ['1', '2', '3'],
                'nested' => [
                    'object' => (static function (): stdClass {
                        $obj       = new stdClass();
                        $obj->name = 'TEST';
                        return $obj;
                    })(),
                    'deep' => ['a' => ['b' => 'C']],
                ],
            ],
        ];
    }

    /**
     * Data provider for get() method tests.
     *
     * @return Generator<string, array{
     *     array: array<string, string>|ArrayObject<string, string>,
     *     key: string,
     *     default: mixed,
     *     expected: mixed
     * }>
     */
    public static function getDataProvider(): Generator
    {
        yield 'array with existing key' => [
            'array'    => ['key' => 'value'],
            'key'      => 'key',
            'default'  => null,
            'expected' => 'value',
        ];

        yield 'array with non-existing key' => [
            'array'    => ['key' => 'value'],
            'key'      => 'nonexistent',
            'default'  => 'default',
            'expected' => 'default',
        ];

        yield 'ArrayAccess with existing key' => [
            'array'    => new ArrayObject(['key' => 'value']),
            'key'      => 'key',
            'default'  => null,
            'expected' => 'value',
        ];

        yield 'ArrayAccess with non-existing key' => [
            'array'    => new ArrayObject(['key' => 'value']),
            'key'      => 'nonexistent',
            'default'  => 'default',
            'expected' => 'default',
        ];
    }

    /**
     * Data provider for groupBy() method tests.
     *
     * @return Generator<string, array{
     *     input: GroupTestArray,
     *     key: string,
     *     expected: GroupTestResult
     * }>
     */
    public static function groupByDataProvider(): Generator
    {
        yield 'simple grouping' => [
            'input' => [
                ['type' => 'fruit', 'name' => 'apple'],
                ['type' => 'vegetable', 'name' => 'carrot'],
                ['type' => 'fruit', 'name' => 'banana'],
            ],
            'key'      => 'type',
            'expected' => [
                'fruit' => [
                    ['type' => 'fruit', 'name' => 'apple'],
                    ['type' => 'fruit', 'name' => 'banana'],
                ],
                'vegetable' => [
                    ['type' => 'vegetable', 'name' => 'carrot'],
                ],
            ],
        ];

        yield 'with missing keys' => [
            'input' => [
                ['type' => 'fruit', 'name' => 'apple'],
                ['name' => 'carrot'],
                ['type' => 'fruit', 'name' => 'banana'],
            ],
            'key'      => 'type',
            'expected' => [
                'fruit' => [
                    ['type' => 'fruit', 'name' => 'apple'],
                    ['type' => 'fruit', 'name' => 'banana'],
                ],
            ],
        ];
    }

    /**
     * Data provider for interlace() method tests.
     *
     * @return Generator<string, array{
     *     arrays: array<int, array<int, int|string>>,
     *     expected: array<int, int|string>|false
     * }>
     */
    public static function interlaceDataProvider(): Generator
    {
        yield 'two arrays' => [
            'arrays'   => [[1, 2], ['a', 'b']],
            'expected' => [1, 'a', 2, 'b'],
        ];

        yield 'single array' => [
            'arrays'   => [[1, 2, 3]],
            'expected' => [1, 2, 3],
        ];

        yield 'different length arrays' => [
            'arrays'   => [[1, 2], ['a']],
            'expected' => [1, 'a', 2],
        ];

        yield 'empty arrays' => [
            'arrays'   => [],
            'expected' => false,
        ];
    }

    /**
     * Data provider for isAssociative() method tests.
     *
     * @return Generator<string, array{
     *     array: array<array-key, mixed>,
     *     expected: bool
     * }>
     */
    public static function isAssociativeDataProvider(): Generator
    {
        yield 'empty array' => [
            'array'    => [],
            'expected' => false,
        ];

        yield 'sequential array' => [
            'array'    => [1, 2, 3],
            'expected' => false,
        ];

        yield 'associative array' => [
            'array'    => ['a' => 1, 'b' => 2],
            'expected' => true,
        ];

        yield 'mixed array' => [
            'array'    => [0 => 'a', 2 => 'b'],
            'expected' => true,
        ];
    }

    /**
     * Data provider for set() method tests.
     *
     * @return Generator<string, array{
     *     array: array<array-key, mixed>|ArrayObject<array-key, mixed>,
     *     key: string|null,
     *     value: mixed,
     *     expected: mixed
     * }>
     */
    public static function setDataProvider(): Generator
    {
        yield 'array with key' => [
            'array'    => [],
            'key'      => 'test',
            'value'    => 'value',
            'expected' => ['test' => 'value'],
        ];

        yield 'array without key' => [
            'array'    => [],
            'key'      => null,
            'value'    => 'value',
            'expected' => 'value',
        ];

        yield 'ArrayAccess' => [
            'array'    => new ArrayObject(),
            'key'      => 'test',
            'value'    => 'value',
            'expected' => ['test' => 'value'],
        ];
    }

    /**
     * Provides test cases for valueExists method.
     *
     * @return Generator<string, array{
     *     array: array<array-key, mixed>,
     *     value: mixed,
     *     expected: bool
     * }>
     */
    public static function valueExistsDataProvider(): Generator
    {
        yield 'existing value' => [
            'array'    => [1, 2, 3],
            'value'    => 2,
            'expected' => true,
        ];

        yield 'non-existing value' => [
            'array'    => [1, 2, 3],
            'value'    => 4,
            'expected' => false,
        ];

        yield 'strict comparison' => [
            'array'    => [1, 2, 3],
            'value'    => '2',
            'expected' => false,
        ];

        yield 'null value exists' => [
            'array'    => [1, null, 3],
            'value'    => null,
            'expected' => true,
        ];

        yield 'array value exists' => [
            'array'    => [1, [2], 3],
            'value'    => [2],
            'expected' => true,
        ];
    }
}
