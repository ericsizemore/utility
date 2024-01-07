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

namespace Esi\Utility\Tests;

use Esi\Utility\Filesystem;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use InvalidArgumentException;
use RuntimeException;

use function array_diff;
use function array_sum;
use function chmod;
use function count;
use function dirname;
use function file_exists;
use function implode;
use function is_dir;
use function mkdir;
use function natsort;
use function range;
use function rmdir;
use function str_replace;
use function touch;
use function trim;
use function unlink;
use function usleep;

use const DIRECTORY_SEPARATOR;

/**
 * File system utility tests.
 */
#[CoversClass(Filesystem::class)]
class FilesystemTest extends TestCase
{
    /**
     * @var string
     */
    protected static string $testDir;

    /**
     * @var array<string>
     */
    protected static array $testFiles;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        self::$testDir = __DIR__ . DIRECTORY_SEPARATOR . 'dir1';
        self::$testFiles = [
            'file1' => self::$testDir . DIRECTORY_SEPARATOR . 'file1',
            'file2' => self::$testDir . DIRECTORY_SEPARATOR . 'file2',
            'file3' => self::$testDir . DIRECTORY_SEPARATOR . 'file3.txt',
        ];

        if (!is_dir(self::$testDir)) {
            mkdir(self::$testDir);
        }

        if (!file_exists(self::$testFiles['file1'])) {
            touch(self::$testFiles['file1']);
        }

        if (!file_exists(self::$testFiles['file2'])) {
            touch(self::$testFiles['file2']);
        }

        if (!file_exists(self::$testFiles['file3'])) {
            touch(self::$testFiles['file3']);
        }
    }

    #[\Override]
    public static function tearDownAfterClass(): void
    {
        unlink(self::$testFiles['file1']);
        unlink(self::$testFiles['file2']);
        unlink(self::$testFiles['file3']);
        usleep(90000);
        rmdir(self::$testDir);

        self::$testDir = '';
        self::$testFiles = [];
    }

    /**
     * Test Filesystem::lineCounter().
     */
    public function testLineCounter(): void
    {
        Filesystem::fileWrite(self::$testFiles['file1'], "This\nis\na\nnew\nline.\n");
        self::assertSame(5, array_sum(Filesystem::lineCounter(directory: self::$testDir, onlyLineCount: true)));
        self::assertSame(0, array_sum(Filesystem::lineCounter(directory: self::$testDir, ignore: ['dir1'], onlyLineCount: true)));
        self::assertSame(0, array_sum(Filesystem::lineCounter(self::$testDir, extensions: ['.txt'], onlyLineCount: true)));

        self::assertSame([
            self::$testDir => [
                'file1' => 5,
                'file2' => 0,
                'file3.txt' => 0,
            ],
        ], Filesystem::lineCounter(directory: self::$testDir));

        self::assertSame([], Filesystem::lineCounter(directory: self::$testDir, ignore: ['dir1']));
        self::assertSame([], Filesystem::lineCounter(self::$testDir, extensions: ['.txt']));

        Filesystem::fileWrite(self::$testFiles['file1']);

        self::expectException(InvalidArgumentException::class);
        $count = array_sum(Filesystem::lineCounter('/this/should/not/exist', onlyLineCount: true));
        $count = count(Filesystem::lineCounter('/this/should/not/exist'));

        $count = array_sum(Filesystem::lineCounter('/this/should/not/exist', ignore: ['dir1'], onlyLineCount: true));
        $count = count(Filesystem::lineCounter('/this/should/not/exist', ignore: ['dir1']));
    }

    /**
     * Test Filesystem::directorySize().
     */
    public function testDirectorySize(): void
    {
        Filesystem::fileWrite(self::$testFiles['file1'], '1234567890');
        Filesystem::fileWrite(self::$testFiles['file2'], implode('', range('a', 'z')));

        self::assertSame(10 + 26, Filesystem::directorySize(self::$testDir));
        self::assertSame(0, Filesystem::directorySize(self::$testDir, ['dir1']));

        Filesystem::fileWrite(self::$testFiles['file1']);
        Filesystem::fileWrite(self::$testFiles['file2']);

        self::expectException(InvalidArgumentException::class);
        $count = Filesystem::directorySize('/this/should/not/exist');
    }

    /**
     * Test Filesystem::directoryList().
     */
    public function testDirectoryList(): void
    {
        Filesystem::fileWrite(self::$testFiles['file1'], '1234567890');
        Filesystem::fileWrite(self::$testFiles['file2'], implode('', range('a', 'z')));

        $expected = [
            0 => self::$testFiles['file1'],
            1 => self::$testFiles['file2'],
        ];
        natsort($expected);

        $actual = Filesystem::directoryList(self::$testDir);
        natsort($actual);

        self::assertSame([], array_diff($expected, $actual));
        self::assertSame([], Filesystem::directoryList(self::$testDir, ['dir1']));

        Filesystem::fileWrite(self::$testFiles['file1']);
        Filesystem::fileWrite(self::$testFiles['file2']);

        self::expectException(InvalidArgumentException::class);
        $count = Filesystem::directoryList('/this/should/not/exist');
    }

    /**
     * Test Filesystem::normalizeFilePath().
     */
    public function testNormalizeFilePath(): void
    {
        $separator = DIRECTORY_SEPARATOR;

        $path1 = __DIR__ . $separator . 'dir1' . $separator . 'file1';
        self::assertSame($path1, Filesystem::normalizeFilePath($path1));

        $path2 = $path1 . $separator;
        self::assertSame($path1, Filesystem::normalizeFilePath($path2));

        $path3 = str_replace($separator, '\\//', $path2);
        self::assertSame($path1, Filesystem::normalizeFilePath($path3));

        $path4 = $path2 . '..' . $separator;
        self::assertSame(str_replace($separator . 'file1', '', $path1), Filesystem::normalizeFilePath($path4));

        $path5 = $path4 . '..';
        self::assertSame(str_replace($separator . 'dir1' . $separator . 'file1', '', $path1), Filesystem::normalizeFilePath($path5));
    }

    /**
     * Test Filesystem::fileRead().
     */
    public function testFileRead(): void
    {
        Filesystem::fileWrite(self::$testFiles['file1'], "This is a test.");

        /** @var string $data */
        $data = Filesystem::fileRead(self::$testFiles['file1']);
        $data = trim($data);

        self::assertSame('This is a test.', $data);

        Filesystem::fileWrite(self::$testFiles['file1']);

        self::expectException(InvalidArgumentException::class);
        $read = Filesystem::fileRead(self::$testFiles['file1'] . '.php');
    }

    /**
     * Test Filesystem::fileWrite().
     */
    public function testFileWrite(): void
    {
        self::assertSame(15, Filesystem::fileWrite(self::$testFiles['file1'], "This is a test."));

        Filesystem::fileWrite(self::$testFiles['file1']);

        self::assertSame(15, Filesystem::fileWrite(self::$testFiles['file1'], "This is a test.", -1));

        self::expectException(InvalidArgumentException::class);
        $read = Filesystem::fileWrite(self::$testFiles['file1'] . '.php');

        chmod(self::$testFiles['file1'], 0o644);
        $read = Filesystem::fileWrite(self::$testFiles['file1']);
    }

    /**
     * Test Filesystem::isReallyWritable().
     */
    public function testIsReallyWritable(): void
    {
        self::assertTrue(Filesystem::isReallyWritable(self::$testDir));
        self::assertTrue(Filesystem::isReallyWritable(self::$testFiles['file1']));
        self::assertTrue(Filesystem::isReallyWritable(self::$testFiles['file2']));

        self::expectException(RuntimeException::class);
        self::assertFalse(Filesystem::isReallyWritable('/this/should/not/exist'));
        self::assertFalse(Filesystem::isReallyWritable('/this/should/not/exist/file'));
    }
}
