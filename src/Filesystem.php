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

namespace Esi\Utility;

// Exceptions
use InvalidArgumentException;
use RuntimeException;
use Random\RandomException;

// Classes
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use SplFileObject;

// Functions
use function is_dir;
use function is_readable;
use function preg_match;
use function count;
use function preg_quote;
use function implode;
use function natsort;
use function rtrim;
use function array_pop;
use function explode;
use function clearstatcache;
use function file_exists;
use function is_writable;
use function file_put_contents;
use function file_get_contents;
use function unlink;
use function sprintf;
use function iterator_count;
use function array_reduce;
use function array_filter;
use function realpath;

// Constants
use const DIRECTORY_SEPARATOR;
use const PHP_OS_FAMILY;
use const FILE_APPEND;

/**
 * File system utilities.
 */
final class Filesystem
{
    /**
     * lineCounter()
     *
     * Parse a given directory's files for an approximate line count. Could be used for
     * a project directory, for example, to determine the line count for a project's codebase.
     *
     * NOTE: It does not count empty lines.
     *
     * @param   string          $directory      Directory to parse.
     * @param   array<string>   $ignore         Subdirectories of $directory you wish
     *                                          to not include in the line count.
     * @param   array<string>   $extensions     An array of file types/extensions of
     *                                          files you want included in the line count.
     * @param   bool            $onlyLineCount  If set to true, only returns an array
     *                                          of line counts without directory/filenames.
     *
     * @return  array<string, array<string, int>>|array<int>
     *
     * @throws  InvalidArgumentException
     */
    public static function lineCounter(string $directory, array $ignore = [], array $extensions = [], bool $onlyLineCount = false): array
    {
        // Sanity check
        if (!Filesystem::isDirectory($directory)) {
            throw new InvalidArgumentException('Invalid $directory specified');
        }

        // Initialize
        $lines = [];

        // Build the actual contents of the directory
        /** @var RecursiveDirectoryIterator $fileInfo */
        foreach (Filesystem::getIterator($directory) as $fileInfo) {
            // Directory names or extensions we wish to ignore
            if (!$fileInfo->isFile()) {
                //@codeCoverageIgnoreStart
                continue;
                //@codeCoverageIgnoreEnd
            }

            if (Filesystem::checkIgnore($fileInfo->getPath(), Filesystem::buildIgnore($ignore))) {
                continue;
            }

            if (Filesystem::checkExtensions($fileInfo->getExtension(), $extensions)) {
                continue;
            }

            $file = new SplFileObject($fileInfo->getPathname());
            $file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

            $lineCount = iterator_count($file);

            if (!$onlyLineCount) {
                /** @var array<string, array<string, int>> $lines */
                $lines[$file->getPath()][$file->getFilename()] = $lineCount;
            } else {
                /** @var array<int> $lines */
                $lines[] = $lineCount;
            }
        }
        return $lines;
    }

    /**
     * directorySize()
     *
     * Retrieves size of a directory (in bytes).
     *
     * @param   string          $directory  Directory to parse.
     * @param   array<string>   $ignore     Subdirectories of $directory you wish to not include.
     * @return  int
     *
     * @throws  InvalidArgumentException
     */
    public static function directorySize(string $directory, array $ignore = []): int
    {
        // Sanity checks
        if (!Filesystem::isDirectory($directory)) {
            throw new InvalidArgumentException('Invalid $directory specified');
        }

        // Initialize
        $size = 0;

        // Determine directory size by checking file sizes
        /** @var RecursiveDirectoryIterator $fileInfo */
        foreach (Filesystem::getIterator($directory) as $fileInfo) {
            // Directories we wish to ignore, if any
            if (Filesystem::checkIgnore($fileInfo->getPath(), Filesystem::buildIgnore($ignore))) {
                continue;
            }

            if ($fileInfo->isFile()) {
                $size += $fileInfo->getSize();
            }
        }
        return $size;
    }

    /**
     * directoryList()
     *
     * Retrieves contents of a directory.
     *
     * @param   string          $directory  Directory to parse.
     * @param   array<string>   $ignore     Subdirectories of $directory you wish to not include.
     * @return  array<mixed>
     *
     * @throws  InvalidArgumentException
     */
    public static function directoryList(string $directory, array $ignore = []): array
    {
        // Sanity checks
        if (!Filesystem::isDirectory($directory)) {
            throw new InvalidArgumentException('Invalid $directory specified');
        }

        // Initialize
        $contents = [];

        // Directories to ignore, if any
        $ignore = Filesystem::buildIgnore($ignore);

        // Build the actual contents of the directory
        /** @var RecursiveDirectoryIterator $fileInfo */
        foreach (Filesystem::getIterator($directory, true) as $key => $fileInfo) {
            if (Filesystem::checkIgnore($fileInfo->getPath(), $ignore)) {
                continue;
            }
            $contents[] = $key;
        }
        natsort($contents);

        return $contents;
    }

    /**
     * normalizeFilePath()
     *
     * Normalizes a file or directory path.
     *
     * @param   string  $path  The file or directory path.
     * @return  string         The normalized file or directory path.
     */
    public static function normalizeFilePath(string $path): string
    {
        // Path will be based on current directory separator as determined by PHP
        $separator = DIRECTORY_SEPARATOR;

        // Clean up the path a bit first
        $path = rtrim(str_replace(['/', '\\'], $separator, $path), $separator);

        // Filter through and reduce as needed
        $filtered = array_filter(
            explode($separator, $path),
            static fn (string $string, bool $binarySafe = false): int => Strings::length($string, $binarySafe)
        );

        $filtered = array_reduce($filtered, static function (array $tmp, string $item): array {
            if ($item === '..') {
                array_pop($tmp);
            } elseif ($item !== '.') {
                $tmp[] = $item;
            }
            return $tmp;
        }, []);

        // Put it all together.
        return implode($separator, $filtered);
    }

    /**
     * isReallyWritable()
     *
     * Checks to see if a file or directory is really writable.
     *
     * @param   string  $file  File or directory to check.
     * @return  bool
     *
     * @throws RandomException  If unable to generate random string for the temp file.
     * @throws RuntimeException If the file or directory does not exist.
     */
    public static function isReallyWritable(string $file): bool
    {
        clearstatcache(true, $file);

        if (!file_exists($file)) {
            throw new RuntimeException('Invalid file or directory specified');
        }

        // If we are on Unix/Linux just run is_writable()
        // @codeCoverageIgnoreStart
        if (PHP_OS_FAMILY !== 'Windows') {
            return is_writable($file);
        }

        // We ignore code coverage due to differences in local and remote testing environments

        // Otherwise, if on Windows...
        // Attempt to write to the file or directory
        if (is_dir($file)) {
            $tmpFile = rtrim($file, '\\/') . DIRECTORY_SEPARATOR . Strings::randomString() . '.txt';
            $data = file_put_contents($tmpFile, 'tmpData', FILE_APPEND);

            unlink($tmpFile);
        } else {
            $data = file_get_contents($file);
        }
        return ($data !== false);
        // @codeCoverageIgnoreEnd
    }

    /**
     * fileRead()
     *
     * Perform a read operation on a pre-existing file.
     *
     * @param   string       $file  Filename.
     * @return  string|false
     *
     * @throws  InvalidArgumentException
     */
    public static function fileRead(string $file): string | false
    {
        // Sanity check
        if (!Filesystem::isFile($file)) {
            throw new InvalidArgumentException(sprintf("File '%s' does not exist or is not readable.", $file));
        }
        return file_get_contents($file);
    }

    /**
     * fileWrite()
     *
     * Perform a write operation on a pre-existing file.
     *
     * @param   string  $file   Filename.
     * @param   string  $data   If writing to the file, the data to write.
     * @param   int     $flags  Bitwise OR'ed set of flags for file_put_contents. One or
     *                          more of FILE_USE_INCLUDE_PATH, FILE_APPEND, LOCK_EX.
     *                          {@link http://php.net/file_put_contents}
     * @return  int<0, max>|false
     *
     * @throws InvalidArgumentException|RandomException
     */
    public static function fileWrite(string $file, string $data = '', int $flags = 0): false | int
    {
        // Sanity checks
        if (!Filesystem::isFile($file)) {
            throw new InvalidArgumentException(sprintf("File '%s' does not exist or is not readable.", $file));
        }

        // @codeCoverageIgnoreStart
        if (!Filesystem::isReallyWritable($file)) {
            throw new InvalidArgumentException(sprintf("File '%s' is not writable.", $file));
        }
        // @codeCoverageIgnoreEnd
        return file_put_contents($file, $data, $flags);
    }

    /** Helper functions for the directory related functions **/
    /**
     * isFile()
     *
     * Determines if the given $file is both a file and readable.
     *
     * @since 2.0.0
     *
     * @param   string  $file  Directory to check.
     * @return  bool
     */
    public static function isFile(string $file): bool
    {
        return (is_file($file) && is_readable($file));
    }

    /**
     * isDirectory()
     *
     * Determines if the given $directory is both a directory and readable.
     *
     * @since 2.0.0
     *
     * @param   string  $directory  Directory to check.
     * @return  bool
     */
    public static function isDirectory(string $directory): bool
    {
        return (is_dir($directory) && is_readable($directory));
    }

    /**
     * Builds the Iterator for lineCounter(), directorySize(), and directoryList().
     *
     * @access protected
     * @since 2.0.0
     *
     * @param   string  $forDirectory  The directory to create an iterator for.
     * @param   bool    $keyAsPath     Whether to use the key as pathname.
     *
     * @return  RecursiveIteratorIterator<RecursiveDirectoryIterator>
     */
    private static function getIterator(string $forDirectory, bool $keyAsPath = false): RecursiveIteratorIterator
    {
        static $options = FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS;

        if ($keyAsPath) {
            $options |= FilesystemIterator::KEY_AS_PATHNAME;
        }

        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($forDirectory, $options)
        );
    }

    /**
     * Builds the ignore list for lineCounter(), directorySize(), and directoryList().
     *
     * @access protected
     * @since 2.0.0
     *
     * @param   array<string>  $ignore  Array of file/folder names to ignore.
     * @return  string
     */
    private static function buildIgnore(array $ignore): string
    {
        if ($ignore !== []) {
            return preg_quote(implode('|', $ignore), '#');
        }
        return '';
    }

    /**
     * Checks the ignore list for lineCounter(), directorySize(), and directoryList().
     *
     * @access protected
     * @since 2.0.0
     *
     * @param   string  $path    The file path to check against ignore list.
     * @param   string  $ignore  The ignore list pattern.
     * @return  bool
     */
    private static function checkIgnore(string $path, string $ignore): bool
    {
        return $ignore !== '' && preg_match("#($ignore)#i", $path) === 1;
    }

    /**
     * Checks the extension ignore list for lineCounter(), directorySize(), and directoryList().
     *
     * @access protected
     * @since 2.0.0
     *
     * @param   string         $extension   File extension to check.
     * @param   array<string>  $extensions  Array of file extensions to ignore.
     * @return  bool
     */
    private static function checkExtensions(string $extension, array $extensions): bool
    {
        return $extensions !== [] && !Arrays::exists($extensions, $extension);
    }
}
