<?php

declare(strict_types = 1);

/**
 * Utility - Collection of various PHP utility functions.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @package   Utility
 * @link      http://www.secondversion.com/
 * @version   1.2.0
 * @copyright (C) 2017 - 2023 Eric Sizemore
 * @license   The MIT License (MIT)
 */
namespace Esi\Utility\Tests;

use Esi\Utility\Utility;
use PHPUnit\Framework\TestCase;

/**
 * Utility - Collection of various PHP utility functions.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @package   Utility
 * @link      http://www.secondversion.com/
 * @version   1.2.0
 * @copyright (C) 2017 - 2023 Eric Sizemore
 * @license   The MIT License (MIT)
 *
 * Copyright (C) 2017 - 2023 Eric Sizemore. All rights reserved.
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

/**
 * Huge shoutout to Brandon Wamboldt and utilphp for the inspiration and some bits of code for some of these tests.
 * @see https://github.com/brandonwamboldt/utilphp/blob/master/tests/UtilTest.php
 */

/**
 * Some of these test methods are... messy. This is just to get testing started.
 *
 * @todo Further refine and improve testing.
 */ 
class UtilityTest extends TestCase
{
    /**
     * Test Utility::getEncoding()
     */
    public function testGetEncoding(): void
    {
        $this->assertEquals('UTF-8', Utility::getEncoding());
    }

    /**
     * Test Utility::setEncoding()
     */
    public function testSetEncoding(): void
    {
        Utility::setEncoding('UCS-2');

        $this->assertEquals('UCS-2', Utility::getEncoding());

        Utility::setEncoding('UTF-8');
    }

    /**
     * Test Utility::arrayFlatten()
     */
    public function testArrayFlatten(): void
    {
        $this->assertEquals([
            0 => 'a',
            1 => 'b',
            2 => 'c',
            3 => 'd',
            '4.first'   => 'e',
            '4.0'       => 'f',
            '4.second'  => 'g',
            '4.1.0'     => 'h',
            '4.1.third' => 'i'
        ], Utility::arrayFlatten([
            'a', 'b', 'c', 'd', ['first' => 'e', 'f', 'second' => 'g', ['h', 'third' => 'i']]
        ]));

        $this->assertEquals(
            [0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd', '4.0' => 'e', '4.1' => 'f', '4.2' => 'g'], 
            Utility::arrayFlatten(['a', 'b', 'c', 'd', ['e', 'f', 'g']])
        );
    }

    /**
     * Test Utility::arrayMapDeep()
     */
    public function testArrayMapDeep(): void
    {
        $this->assertEquals([
            '&lt;',
            'abc',
            '&gt;',
            'def',
            ['&amp;', 'test', '123']
        ], Utility::arrayMapDeep([
            '<',
            'abc',
            '>',
            'def',
            ['&', 'test', '123']
        ], 'htmlentities'));
    }

    /**
     * Test Utility::arrayInterlace()
     */
    public function testArrayInterlace(): void
    {
        $input = Utility::arrayInterlace([1, 2, 3], ['a', 'b', 'c']);
        $expect = [1, 'a', 2, 'b', 3, 'c'];

        $this->assertEquals($expect, $input);
    }

    /**
     * Test Utility::title()
     */
    public function testTitle(): void
    {
        $title = Utility::title('Mary had A little lamb and She Loved it so');
        $this->assertEquals('Mary Had A Little Lamb And She Loved It So', $title);
    }

    /**
     * Test Utility::lower()
     */
    public function testLower(): void
    {
        $this->assertEquals('test', Utility::lower('tESt'));
        $this->assertEquals('test', Utility::lower('TEST'));
    }

    /**
     * Test Utility::upper()
     */
    public function testUpper(): void
    {
        $this->assertEquals('TEST', Utility::upper('teSt'));
    }

    /**
     * Test Utility::substr()
     */
    public function testSubstr(): void
    {
        $this->assertEquals('f', Utility::substr('abcdef', -1));
    }

    /**
     * Test Utility::lcfirst()
     */
    public function testLcfirst(): void
    {
        $this->assertEquals('test', Utility::lcfirst('Test'));
        $this->assertEquals('tEST', Utility::lcfirst('TEST'));
    }

    /**
     * Test Utility::ucfirst()
     */
    public function testUcfirst(): void
    {
        $this->assertEquals('Test', Utility::ucfirst('test'));
        $this->assertEquals('TEsT', Utility::ucfirst('tEsT'));
    }

    /**
     * Test Utility::strcasecmp()
     */
    public function testStrcasecmp(): void
    {
        //Returns -1 if string1 is less than string2; 1 if string1 is greater than string2, and 0 if they are equal.
        $str1 = 'test';
        $str2 = 'Test';

        $this->assertEquals(0, Utility::strcasecmp($str1, $str2));

        $str1 = 'tes';
        $str2 = 'Test';

        $this->assertEquals(-1, Utility::strcasecmp($str1, $str2));

        $str1 = 'testing';
        $str2 = 'Test';

        $this->assertEquals(1, Utility::strcasecmp($str1, $str2));
    }

    /**
     * Test Utility::beginsWith()
     */
    public function testBeginsWith(): void
    {
        $this->assertTrue(Utility::beginsWith('this is a test', 'this'));
        $this->assertFalse(Utility::beginsWith('this is a test', 'test'));
    }

    /**
     * Test Utility::endsWith()
     */
    public function testEndsWith(): void
    {
        $this->assertTrue(Utility::endsWith('this is a test', 'test'));
        $this->assertFalse(Utility::endsWith('this is a test', 'this'));
    }

    /**
     * Test Utility::doesContain()
     */
    public function testDoesContain(): void
    {
        $this->assertTrue(Utility::doesContain('start a string', 'a string'));
        $this->assertFalse(Utility::doesContain('start a string', 'starting'));
    }

    /**
     * Test Utility::doesNotContain()
     */
    public function testDoesNotContain(): void
    {
        $this->assertTrue(Utility::doesNotContain('start a string', 'stringly'));
        $this->assertFalse(Utility::doesNotContain('start a string', 'string'));
    }

    /**
     * Test Utility::length()
     */
    public function testLength(): void
    {
        $this->assertEquals(14, Utility::length('this is a test'));
    }

    /**
     * Test Utility::ascii()
     */
    public function testAscii(): void
    {
        $this->assertEquals("AA ", Utility::ascii("ǍǺ\xE2\x80\x87"));
    }

    /**
     * Test Utility::slugify()
     */
    public function testSlugify(): void
    {
        $this->assertEquals('a-simple-title', Utility::slugify('A simple title'));
        $this->assertEquals('this-post-it-has-a-dash', Utility::slugify('This post -- it has a dash'));
        $this->assertEquals('123-1251251', Utility::slugify('123----1251251'));

        $this->assertEquals('a-simple-title', Utility::slugify('A simple title', '-'));
        $this->assertEquals('this-post-it-has-a-dash', Utility::slugify('This post -- it has a dash', '-'));
        $this->assertEquals('123-1251251', Utility::slugify('123----1251251', '-'));

        $this->assertEquals('a_simple_title', Utility::slugify('A simple title', '_'));
        $this->assertEquals('this_post_it_has_a_dash', Utility::slugify('This post -- it has a dash', '_'));
        $this->assertEquals('123_1251251', Utility::slugify('123----1251251', '_'));
    }

    /**
     * Test Utility::randomBytes()
     */
    public function testRandomBytes(): void
    {
        $bytes = Utility::randomBytes(8);
        $this->assertNotEmpty($bytes);
    }

    /**
     * Test Utility::randomInt()
     */
    public function testRandomInt(): void
    {
        $int = Utility::randomInt(100, 250);
        $this->assertTrue(($int >= 100 and $int <= 250));
    }

    /**
     * Test Utility::randomString()
     */
    public function testRandomString(): void
    {
        $str = Utility::randomString(16);
        $this->assertTrue(Utility::length($str) === 16);
    }

    /**
     * Test Utility::lineCounter()
     */
    public function testLineCounter(): void
    {
        $dir = \dirname(__FILE__) . \DIRECTORY_SEPARATOR . 'dir1';
        \mkdir($dir);

        $file1 = $dir . \DIRECTORY_SEPARATOR . 'file1';
        \touch($file1);

        Utility::fileWrite($file1, "This\nis\na\nnew\nline.\n");

        $this->assertEquals(5, \array_sum(Utility::lineCounter(directory: $dir, onlyLineCount: true)));

        \unlink($file1);
        \rmdir($dir);
    }

    /**
     * Test Utility::directorySize()
     */
    public function testDirectorySize(): void
    {
        $dir = \dirname(__FILE__) . \DIRECTORY_SEPARATOR . 'dir1';
        \mkdir($dir);

        $file1 = $dir . \DIRECTORY_SEPARATOR . 'file1';
        \touch($file1);

        Utility::fileWrite($file1, '1234567890');

        $file2 = $dir . \DIRECTORY_SEPARATOR . 'file2';
        \touch($file2);

        Utility::fileWrite($file2, \implode('', \range('a', 'z')));

        $this->assertEquals(10 + 26, Utility::directorySize($dir));

        \unlink($file1);
        \unlink($file2);
        \rmdir($dir);
    }

    /**
     * Test Utility::directoryList()
     */
    public function testDirectoryList(): void
    {
        $dir = \dirname(__FILE__) . \DIRECTORY_SEPARATOR . 'dir1';
        \mkdir($dir);

        $file1 = $dir . \DIRECTORY_SEPARATOR . 'file1';
        \touch($file1);

        Utility::fileWrite($file1, '1234567890');

        $file2 = $dir . \DIRECTORY_SEPARATOR . 'file2';
        \touch($file2);

        Utility::fileWrite($file2, \implode('', \range('a', 'z')));

        $expected = [
            0 => $file2,
            1 => $file1
        ];

        $this->assertEquals($expected, Utility::directoryList($dir));

        \unlink($file1);
        \unlink($file2);
        \rmdir($dir);
    }

    /**
     * Test Utility::normalizeFilePath()
     */
    public function testNormalizeFilePath(): void
    {
        $path1 = \dirname(__FILE__) . \DIRECTORY_SEPARATOR . 'dir1'. \DIRECTORY_SEPARATOR . 'file1';
        $this->assertEquals($path1, Utility::normalizeFilePath($path1));

        $path2 = \dirname(__FILE__) . \DIRECTORY_SEPARATOR . 'dir1'. \DIRECTORY_SEPARATOR . 'file1'. \DIRECTORY_SEPARATOR;
        $this->assertEquals($path1, Utility::normalizeFilePath($path2));

        $path3 = \str_replace(\DIRECTORY_SEPARATOR, '\\//', $path2);
        $this->assertEquals($path1, Utility::normalizeFilePath($path3));
    }

    /**
     * Test Utility::fileRead()
     */
    public function testFileRead(): void
    {
        $dir = \dirname(__FILE__) . \DIRECTORY_SEPARATOR . 'dir1';

        if (!\is_dir($dir)) {
            \mkdir($dir);
        }

        $file1 = $dir . \DIRECTORY_SEPARATOR . 'file1';

        if (!\file_exists($file1)) {
            \touch($file1);
        }

        Utility::fileWrite($file1, "This is a test.");

        /** @var string $data **/
        $data = Utility::fileRead($file1);
        $data = \trim($data);

        $this->assertEquals('This is a test.', $data);

        if (\unlink($file1)) {
            \rmdir($dir);
        }
    }

    /**
     * Test Utility::fileWrite()
     */
    public function testFileWrite(): void
    {
        $dir = \dirname(__FILE__) . \DIRECTORY_SEPARATOR . 'dir1';

        if (!\is_dir($dir)) {
            \mkdir($dir);
        }

        $file1 = $dir . \DIRECTORY_SEPARATOR . 'file1';

        if (!\file_exists($file1)) {
            \touch($file1);
        }

        $this->assertEquals(15, Utility::fileWrite($file1, "This is a test."));

        if (\unlink($file1)) {
            \rmdir($dir);
        }
    }

    /**
     * Test Utility::fahrenheitToCelsius()
     */
    public function testFahrenheitToCelsius(): void
    {
        $this->assertEquals(23.33, Utility::fahrenheitToCelsius(74));
        $this->assertEquals(23.333333333333332, Utility::fahrenheitToCelsius(74, false));
    }

    /**
     * Test Utility::celsiusToFahrenheit()
     */
    public function testCelsiusToFahrenheit(): void
    {
        $this->assertEquals(73.99, Utility::celsiusToFahrenheit(23.33));
        $this->assertEquals(74, Utility::celsiusToFahrenheit(23.333333333333332, false));
    }

    /**
     * Test Utility::celsiusToKelvin()
     */
    public function testCelsiusToKelvin(): void
    {
        $this->assertEquals(296.48, Utility::celsiusToKelvin(23.33));
        $this->assertEquals(296.4833333333333, Utility::celsiusToKelvin(23.333333333333332, false));
    }

    /**
     * Test Utility::kelvinToCelsius()
     */
    public function testKelvinToCelsius(): void
    {
        $this->assertEquals(23.33, Utility::kelvinToCelsius(296.48));
        $this->assertEquals(23.333333333333314, Utility::kelvinToCelsius(296.4833333333333, false));
    }

    /**
     * Test Utility::fahrenheitToKelvin()
     */
    public function testFahrenheitToKelvin(): void
    {
        $this->assertEquals(296.48, Utility::fahrenheitToKelvin(74));
        $this->assertEquals(296.4833333333333, Utility::fahrenheitToKelvin(74, false));
    }

    /**
     * Test Utility::kelvinToFahrenheit()
     */
    public function testKelvinToFahrenheit(): void
    {
        $this->assertEquals(73.99, Utility::kelvinToFahrenheit(296.48));
        $this->assertEquals(73.99999999999997, Utility::kelvinToFahrenheit(296.4833333333333, false));
    }

    /**
     * Test Utility::fahrenheitToRankine()
     */
    public function testFahrenheitToRankine(): void
    {
        $this->assertEquals(533.67, Utility::fahrenheitToRankine(74));
        $this->assertEquals(533.6700000000001, Utility::fahrenheitToRankine(74, false));
    }

    /**
     * Test Utility::rankineToFahrenheit()
     */
    public function testRankineToFahrenheit(): void
    {
        $this->assertEquals(74, Utility::rankineToFahrenheit(533.67));
        $this->assertEquals(74.00000000000006, Utility::rankineToFahrenheit(533.6700000000001, false));
    }

    /**
     * Test Utility::celsiusToRankine()
     */
    public function testCelsiusToRankine(): void
    {
        $this->assertEquals(545.67, Utility::celsiusToRankine(30));
        $this->assertEquals(545.6700000000001, Utility::celsiusToRankine(30, false));
    } 

    /**
     * Test Utility::rankineToCelsius()
     */
    public function testRankineToCelsius(): void
    {
        $this->assertEquals(30, Utility::rankineToCelsius(545.67));
        $this->assertEquals(29.999999999999968, Utility::rankineToCelsius(545.67, false));
    }

    /**
     * Test Utility::kelvinToRankine()
     */
    public function testKelvinToRankine(): void
    {
        $this->assertEquals(234.0, Utility::kelvinToRankine(130));
        $this->assertEquals(234.00000000000006, Utility::kelvinToRankine(130, false));
    }

    /**
     * Test Utility::rankineToKelvin()
     */
    public function testRankineToKelvin(): void
    {
        $this->assertEquals(130, Utility::rankineToKelvin(234.0));
        $this->assertEquals(129.99999999999997, Utility::rankineToKelvin(234.0, false));
    }

    /**
     * Test Utility::validEmail()
     */
    public function testValidEmail(): void
    {
        $this->assertTrue(Utility::validEmail('john.smith@gmail.com'));
        $this->assertTrue(Utility::validEmail('john.smith+label@gmail.com'));
        $this->assertTrue(Utility::validEmail('john.smith@gmail.co.uk'));
    }

    /**
     * Test Utility::validJson()
     */
    public function testValidJson(): void
    {
        $this->assertTrue(Utility::validJson('{ "test": { "foo": "bar" } }'));
        $this->assertFalse(Utility::validJson('{ "": "": "" } }'));
    }

    /**
     * Test Utility::sizeFormat()
     */
    public function testSizeFormat(): void
    {
        $size = Utility::sizeFormat(512);
        $this->assertEquals('512 B', $size);

        $size = Utility::sizeFormat(2048, 1);
        $this->assertEquals('2.0 KiB', $size);

        $size = Utility::sizeFormat(25151251, 2);
        $this->assertEquals('23.99 MiB', $size);

        $size = Utility::sizeFormat(19971597926, 2);
        $this->assertEquals('18.60 GiB', $size);

        $size = Utility::sizeFormat(2748779069440, 1);
        $this->assertEquals('2.5 TiB', $size);

        $size = Utility::sizeFormat(2748779069440 * 1024, 1);
        $this->assertEquals('2.5 PiB', $size);

        $size = Utility::sizeFormat(2748779069440 * (1024 * 1024), 1);
        $this->assertEquals('2.5 EiB', $size);
    }

    /**
     * Test Utility::timeDifference()
     */
    public function testTimeDifference(): void
    {
        $this->assertEquals('1 second(s) old', Utility::timeDifference(\time() - 1));
        $this->assertEquals('30 second(s) old', Utility::timeDifference(\time() - 30));
        $this->assertEquals('1 minute(s) old', Utility::timeDifference(\time() - 60));
        $this->assertEquals('5 minute(s) old', Utility::timeDifference(\time() - (60 * 5)));
        $this->assertEquals('1 hour(s) old', Utility::timeDifference(\time() - (3600)));
        $this->assertEquals('2 hour(s) old', Utility::timeDifference(\time() - (3600 * 2)));
        $this->assertEquals('1 day(s) old', Utility::timeDifference(\time() - (3600 * 24)));
        $this->assertEquals('5 day(s) old', Utility::timeDifference(\time() - (3600 * 24 * 5)));
        $this->assertEquals('1 week(s) old', Utility::timeDifference(\time() - (3600 * 24 * 7)));
        $this->assertEquals('2 week(s) old', Utility::timeDifference(\time() - (3600 * 24 * 14)));
        $this->assertEquals('1 month(s) old', Utility::timeDifference(\time() - (604800 * 5)));
        $this->assertEquals('2 month(s) old', Utility::timeDifference(\time() - (604800 * 10)));
        $this->assertEquals('1 year(s) old', Utility::timeDifference(\time() - (2592000 * 15)));
        $this->assertEquals('2 year(s) old', Utility::timeDifference(\time() - (2592000 * 36)));
        $this->assertEquals('11 year(s) old', Utility::timeDifference(\time() - (2592000 * 140)));
    }

    /**
     * Test Utility::getIpAddress()
     */
    public function testGetIpAddress(): void
    {
        $_SERVER['REMOTE_ADDR'] = '1.1.1.1';
        $_SERVER['HTTP_CLIENT_IP'] = '1.1.1.2';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.1.1.3';
        $_SERVER['HTTP_X_REAL_IP'] = '1.1.1.4';

        $this->assertEquals('1.1.1.1', Utility::getIpAddress());
        $this->assertEquals('1.1.1.3', Utility::getIpAddress(true));

        unset($_SERVER['HTTP_X_FORWARDED_FOR']);

        $this->assertEquals('1.1.1.4', Utility::getIpAddress(true));

        unset($_SERVER['HTTP_X_REAL_IP']);

        $this->assertEquals('1.1.1.2', Utility::getIpAddress(true));
    }

    /**
     * Test Utility::isPrivateIp()
     */
    public function testIsPrivateIp(): void
    {
        $this->assertTrue(Utility::isPrivateIp('192.168.0.0'));
        $this->assertFalse(Utility::isPrivateIp('1.1.1.1'));
    }

    /**
     * Test Utility::isReservedIp()
     */
    public function testIsReservedIp(): void
    {
        $this->assertTrue(Utility::isReservedIp('0.255.255.255'));
        $this->assertFalse(Utility::isReservedIp('192.168.0.0'));
    }

    /**
     * Test Utility::isPublicIp()
     */
    public function testIsPublicIp(): void
    {
        $this->assertTrue(Utility::isPublicIp('1.1.1.1'));
        $this->assertFalse(Utility::isPublicIp('192.168.0.0'));
        $this->assertFalse(Utility::isPublicIp('0.255.255.255'));
    }

    /**
     * Test Utility::obscureEmail()
     */
    public function testObscureEmail(): void
    {
        $this->assertEquals(
            '&#97;&#100;&#109;&#105;&#110;&#64;&#115;&#101;&#99;&#111;&#110;&#100;&#118;&#101;&#114;&#115;&#105;&#111;&#110;&#46;&#99;&#111;&#109;',
            Utility::obscureEmail('admin@secondversion.com')
        );
    }

    /**
     * Test Utility::currentHost()
     */
    public function testCurrentHost(): void
    {
        $_SERVER['HTTP_HOST'] = 'example.com';

        $this->assertEquals('example.com', Utility::currentHost());

        unset($_SERVER['HTTP_HOST']);
    }

    /**
     * Test Utility::serverHttpVars()
     */
    public function testServerHttpVars(): void
    {
        // Shouldn't be any in CLI
        $this->assertEquals([], Utility::serverHttpVars());
    }

    /**
     * Test Utility::isHttps()
     */
    public function testIsHttps(): void
    {
        $_SERVER['HTTPS'] = null;

        $this->assertFalse(Utility::isHttps());

        $_SERVER['HTTPS'] = 'on';

        $this->assertTrue(Utility::isHttps());

        $_SERVER['HTTPS'] = null;
    }

    /**
     * Test Utility::currentUrl()
     */
    public function testCurrentUrl(): void
    {
        $expected = 'http://test.dev/test.php?foo=bar';
        $expectedAuth = 'http://admin:123@test.dev/test.php?foo=bar';
        $expectedPort = 'http://test.dev:443/test.php?foo=bar';
        $expectedPort2 = 'https://test.dev:80/test.php?foo=bar';
        $expectedSSL = 'https://test.dev/test.php?foo=bar';

        $_SERVER['HTTP_HOST'] = 'test.dev';
        $_SERVER['REQUEST_URI'] = '/test.php?foo=bar';
        $_SERVER['QUERY_STRING'] = 'foo=bar';
        $_SERVER['PHP_SELF'] = '/test.php';

        // Test basic url
        $this->assertEquals($expected, Utility::currentUrl());

        // Test server auth.
        $_SERVER['PHP_AUTH_USER'] = 'admin';
        $_SERVER['PHP_AUTH_PW'] = '123';
        $this->assertEquals($expectedAuth, Utility::currentUrl());

        unset($_SERVER['PHP_AUTH_USER']);
        unset($_SERVER['PHP_AUTH_PW']);

        // Test port.
        $_SERVER['SERVER_PORT'] = 443;
        $this->assertEquals($expectedPort, Utility::currentUrl());

        // Test SSL.
        $_SERVER['HTTPS'] = 'on';
        $this->assertEquals($expectedSSL, Utility::currentUrl());

        $_SERVER['SERVER_PORT'] = 80;
        $this->assertEquals($expectedPort2, Utility::currentUrl());
 
        unset($_SERVER['HTTPS']);

        // Test no $_SERVER['REQUEST_URI'] (e.g., MS IIS).
        unset($_SERVER['REQUEST_URI']);
        $this->assertEquals($expected, Utility::currentUrl());
    }

    /**
     * Test Utility::ordinal()
     */
    public function testOrdinal(): void
    {
        $this->assertEquals('1st', Utility::ordinal(1));
        $this->assertEquals('2nd', Utility::ordinal(2));
        $this->assertEquals('3rd', Utility::ordinal(3));
        $this->assertEquals('4th', Utility::ordinal(4));
        $this->assertEquals('5th', Utility::ordinal(5));
        $this->assertEquals('6th', Utility::ordinal(6));
        $this->assertEquals('7th', Utility::ordinal(7));
        $this->assertEquals('8th', Utility::ordinal(8));
        $this->assertEquals('9th', Utility::ordinal(9));
        $this->assertEquals('22nd', Utility::ordinal(22));
        $this->assertEquals('23rd', Utility::ordinal(23));
        $this->assertEquals('143rd', Utility::ordinal(143));
    }

    /**
     * Test Utility::statusHeader()
     */
    public function testStatusHeader(): void
    {
        Utility::statusHeader(200);

        $this->assertEquals(200, http_response_code());

        Utility::statusHeader(500);

        $this->assertEquals(500, http_response_code());
    }

    /**
     * Test Utility::guid()
     */
    public function testGuid(): void
    {
        $guid = Utility::guid();
        $this->assertMatchesRegularExpression('/^[0-9A-F]{8}-[0-9A-F]{4}-[4][0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $guid);
    }

    /**
     * Test Utility::timezoneInfo()
     */
    public function testTimezoneInfo(): void
    {
        $zoneInfo = Utility::timezoneInfo('America/New_York');
        $expected = ($zoneInfo['dst'] === 1) ? -4 : -5;

        $this->assertEquals($expected, $zoneInfo['offset']);
        $this->assertEquals('US', $zoneInfo['country']);
    }

    /**
     * Test Utility::iniGet()
     */
    public function testIniGet(): void
    {
        $this->assertNotEmpty(Utility::iniGet('request_order'));
    }

    /**
     * Test Utility::iniSet()
     */
    public function testIniSet(): void
    {
        /** @var string $oldValue **/
        $oldValue = Utility::iniSet('display_errors', (string)Utility::iniGet('display_errors'));

        $this->assertEquals($oldValue, Utility::iniSet('display_errors', $oldValue));
    }
}
