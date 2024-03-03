# Filesystem

`Esi\Utility\Filesystem`

* [lineCounter](#linecounter)(string $directory, array $ignore = [], array $extensions = [], bool $onlyLineCount = false): array;
* [directorySize](#directorysize)(string $directory, array $ignore = []): int;
* [directoryList](#directorylist)(string $directory, array $ignore = []): array;
* [normalizeFilePath](#normalizefilepath)(string $path): string;
* [isReallyWritable](#isreallywritable)(string $file): bool;
* [fileRead](#fileread)(string $file): string | false;
* [fileWrite](#filewrite)(string $file, string $data = '', int $flags = 0): string | false | int;
* [isFile](#isfile)(string $file): bool;
* [isDirectory](#isdirectory)(string $directory): bool;

#### @access private
```php
// Helper functions for lineCounter(), directorySize(), directoryList()
getIterator(string $forDirectory, bool $keyAsPath = false): RecursiveIteratorIterator
buildIgnore(array $ignore): string
checkIgnore(string $path, string $ignore): bool
checkExtensions(string $extension, array $extensions): bool
```


## lineCounter

Parse a given directory's files for an approximate line count. Could be used for a project directory, for example, to determine the line count for a project's codebase.

```php
use Esi\Utility\Filesystem;

/**
 * For this example, let's say '/some/directory' has 3 files:
 *    file1
 *    file2
 *    file3.txt
 *
 * ... and 'file1' has 5 lines.
 */
$directory = '/some/directory';

print_r(Filesystem::lineCounter($directory));

/*
Array (
    '/some/directory' => Array (
        'file1' => 0,
        'file2' => 0,
        'file3.txt' => 5
    )
)
*/
```

## directorySize

Retrieves size of a directory (in bytes).

```php
use Esi\Utility\Filesystem;

/**
 * For this example, let's say '/some/directory' has 2 files:
 *    file1 with content '1234567890'
 *    file2 with content 'abcdefghijklmnopqrstuvwxyz'
 */
$directory = '/some/directory';

echo Filesystem::directorySize($directory); // 36
```

## directoryList

Retrieves contents of a directory.

```php
use Esi\Utility\Filesystem;

/**
 * For this example, let's say '/some/directory' has 2 files:
 *    file1
 *    file2
 */
$directory = '/some/directory';

print_r(Filesystem::directoryList($directory));

/*
Array (
    0 => '/some/directory/file1',
    1 => '/some/directory/file2',
)
*/
```

## normalizeFilePath

Normalizes a file or directory path.

```php
use Esi\Utility\Filesystem;

$separator = DIRECTORY_SEPARATOR;
$path1 = $separator . 'some' . $separator . 'directory' . $separator . 'file1';

echo Filesystem::normalizeFilePath($path1); // /some/directory/file1

$path2 = '\\//some\\//directory\\//file1';
echo Filesystem::normalizeFilePath($path2); // /some/directory/file1
```

## isReallyWritable

Checks to see if a file or directory is really writable.

```php
use Esi\Utility\Filesystem;

$file = '/this/file/exists_writable.txt';

var_dump(Filesystem::isReallyWritable($file)); // bool(true)
```

## fileRead

Perform a read operation on a pre-existing file.

```php
use Esi\Utility\Filesystem;

// This is essentially a wrapper around 'file_get_contents' that will throw an exception if not a file.
echo Filesystem::fileRead('/some/directory/file1'); // '1234567890'
```

## fileWrite

Perform a write operation on a pre-existing file.

```php
use Esi\Utility\Filesystem;

// A wrapper around 'file_put_contents' that checks file existence and its writability.
echo Filesystem::fileWrite('/some/directory/file1', "This is a test."); // 15
```

## isFile

Determines if the given $file is both a file and readable.

```php
// Just combines 'is_file' and 'is_readable' checks.
```

## isDirectory

Determines if the given $directory is both a directory and readable.

```php
// Just combines 'is_dir' and 'is_readable' checks.
```
