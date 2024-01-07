# Arrays

`Esi\Utility\Arrays`

* [isAssociative](#isassociative)(array $array): bool
* [get](#get)(array | ArrayAccess $array, string | int $key, mixed $default = null): mixed
* [set](#set)(array | ArrayAccess &$array, string | int | null $key, mixed $value): mixed
* [exists](#exists)(array | ArrayAccess $array, string | int $key): bool
* [flatten](#flatten)(array $array, string $separator = '.', string $prepend = ''): array
* [mapDeep](#mapdeep)(mixed $array, callable $callback): mixed
* [interlace](#interlace)(array ...$args): array | false
* [groupBy](#groupby)(array $array, string $key): array


## isAssociative

Determines if the given array is an associative array.

```php
use Esi\Utility\Arrays;

$array = [0, 1, 2, 3, 4];
$arrayTwo = ['test' => 'testing', 'testing' => 'what'];

var_dump(Arrays::isAssociative($array)); // bool(false)
var_dump(Arrays::isAssociative($arrayTwo)); // bool(true)
```

## get

Retrieve a value from an array.

```php
use Esi\Utility\Arrays;

$array = ['this' => 'is', 'a' => 'test'];

var_dump(Arrays::get($array, 'this')); // 'is'
var_dump(Arrays::get($array, 'a')); // 'test'
var_dump(Arrays::get($array, 'notexist')); // null
```

## set

Add a value to an array.

```php
use Esi\Utility\Arrays;

$array = ['this' => 1, 'is' => 2, 'a' => 3, 'test' => 4];
$newArray = ['that' => 4, 'was' => 3, 'a' => 2, 'test' => 1];

Arrays::set($array, 'test', 5);
var_dump(Arrays::get($array, 'test')); // 5

Arrays::set($array, null, $newArray);
var_dump(Arrays::get($array, 'that')); // 4
```

## exists

Check if a key exists in an array.

```php
use Esi\Utility\Arrays;

/**
 * @implements ArrayAccess<mixed, mixed>
 */
class TestArrayAccess implements \ArrayAccess
{
    /**
     * @var array<int|string, mixed>
     */
    public array $container = [
        "one"   => 1,
        "two"   => 2,
        "three" => 3,
    ];

    /**
     * Set an offset
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Whether an offset exists
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Unset an offset
     *
     * @param mixed $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Retrieve an offset exists
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->container[$offset] ?? null;
    }
}

$array = ['test' => 1];

$arrayAccess = new TestArrayAccess();
$arrayAccess['test'] = 1;

var_dump(Arrays::exists($array, 'test')); // bool(true)
var_dump(Arrays::exists($array, 'this')); // bool(false)

var_dump(Arrays::exists($arrayAccess, 'test')); // bool(true)
var_dump(Arrays::exists($arrayAccess, 'this')); // bool(false)
```

## flatten

Flattens a multidimensional array.

```php
use Esi\Utility\Arrays;

$array = Arrays::flatten(['a', 'b', 'c', 'd', ['e', 'f', 'g']]);

// [0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd', '4.0' => 'e', '4.1' => 'f', '4.2' => 'g']);            
```

## mapDeep

Recursively applies a callback to all non-iterable elements of an array or an object.

```php
use Esi\Utility\Arrays;

$mapDeep = Arrays::mapDeep([
    '<',
    'abc',
    '>',
    'def',
    ['&', 'test', '123']
], 'htmlentities');

/*
$mapDeep = [
    '&lt;',
    'abc',
    '&gt;',
    'def',
    ['&amp;', 'test', '123']
];
*/
```

## interlace

Interlaces one or more arrays' values (not preserving keys).

```php
use Esi\Utility\Arrays;

$interlace = Arrays::interlace([1, 2, 3], ['a', 'b', 'c']);
// [1, 'a', 2, 'b', 3, 'c']
```

## groupBy

Returns an associative array, grouped by $key, where the keys are the distinct values of $key, 
and the values are arrays of items that share the same $key.

*Important to note:* if a $key is provided that does not exist, the result will be an empty array.

```php
use Esi\Utility\Arrays;

$grouped = Arrays::groupBy([
    ['id' => 1, 'category' => 'A', 'value' => 'foo'],
    ['id' => 2, 'category' => 'B', 'value' => 'bar'],
    ['id' => 3, 'category' => 'A', 'value' => 'baz'],
    ['id' => 4, 'category' => 'B', 'value' => 'qux']
], 'category');

print_r($grouped);

/*
Array
(
    [A] => Array
        (
            [0] => Array
                (
                    [id] => 1
                    [category] => A
                    [value] => foo
                )

            [1] => Array
                (
                    [id] => 3
                    [category] => A
                    [value] => baz
                )

        )

    [B] => Array
        (
            [0] => Array
                (
                    [id] => 2
                    [category] => B
                    [value] => bar
                )

            [1] => Array
                (
                    [id] => 4
                    [category] => B
                    [value] => qux
                )

        )

)
*/
```
