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


## 



```php

```

## 



```php

```

## 



```php

```

## 



```php

```

## 



```php

```

## 



```php

```

## 



```php

```

## 



```php

```

## 



```php

```
