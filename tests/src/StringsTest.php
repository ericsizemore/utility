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

use Esi\Utility\Strings;
use Esi\Utility\Environment;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Random\RandomException;
use InvalidArgumentException;

/**
 * String utility tests.
 */
#[CoversClass(Strings::class)]
class StringsTest extends TestCase
{
    /**
     * Test Strings::getEncoding().
     */
    public function testGetEncoding(): void
    {
        self::assertSame('UTF-8', Strings::getEncoding());
    }

    /**
     * Test Strings::setEncoding().
     */
    public function testSetEncoding(): void
    {
        // With no ini update.
        Strings::setEncoding('UCS-2');

        self::assertSame('UCS-2', Strings::getEncoding());

        Strings::setEncoding('UTF-8');

        // With ini update.
        Strings::setEncoding('UCS-2', true);

        self::assertSame('UCS-2', Strings::getEncoding());
        self::assertSame('UCS-2', Environment::iniGet('default_charset'));
        self::assertSame('UCS-2', Environment::iniGet('internal_encoding'));

        Strings::setEncoding('UTF-8', true);
    }

    /**
     * Test Strings::title().
     */
    public function testTitle(): void
    {
        $title = Strings::title('Mary had A little lamb and She Loved it so');
        self::assertSame('Mary Had A Little Lamb And She Loved It So', $title);
    }

    /**
     * Test Strings::lower().
     */
    public function testLower(): void
    {
        self::assertSame('test', Strings::lower('tESt'));
        self::assertSame('test', Strings::lower('TEST'));
    }

    /**
     * Test Strings::upper().
     */
    public function testUpper(): void
    {
        self::assertSame('TEST', Strings::upper('teSt'));
    }

    /**
     * Test Strings::substr().
     */
    public function testSubstr(): void
    {
        self::assertSame('f', Strings::substr('abcdef', -1));
    }

    /**
     * Test Strings::lcfirst().
     */
    public function testLcfirst(): void
    {
        self::assertSame('test', Strings::lcfirst('Test'));
        self::assertSame('tEST', Strings::lcfirst('TEST'));
    }

    /**
     * Test Strings::ucfirst().
     */
    public function testUcfirst(): void
    {
        self::assertSame('Test', Strings::ucfirst('test'));
        self::assertSame('TEsT', Strings::ucfirst('tEsT'));
    }

    /**
     * Test Strings::strcasecmp().
     */
    public function testStrcasecmp(): void
    {
        // Returns -1 if string1 is less than string2; 1 if string1 is greater than string2, and 0 if they are equal.
        $str1 = 'test';
        $str2 = 'Test';

        self::assertSame(0, Strings::strcasecmp($str1, $str2));

        $str1 = 'tes';

        self::assertSame(-1, Strings::strcasecmp($str1, $str2));

        $str1 = 'testing';

        self::assertSame(1, Strings::strcasecmp($str1, $str2));
    }

    /**
     * Test Strings::beginsWith().
     */
    public function testBeginsWith(): void
    {
        self::assertTrue(Strings::beginsWith('this is a test', 'this'));
        self::assertFalse(Strings::beginsWith('this is a test', 'test'));

        self::assertTrue(Strings::beginsWith('THIS IS A TEST', 'this', true));
        self::assertFalse(Strings::beginsWith('THIS IS A TEST', 'test', true));

        self::assertTrue(Strings::beginsWith('THIS IS A TEST', 'this', true, true));
        self::assertFalse(Strings::beginsWith('THIS IS A TEST', 'test', true, true));
    }

    /**
     * Test Strings::endsWith().
     */
    public function testEndsWith(): void
    {
        self::assertTrue(Strings::endsWith('this is a test', 'test'));
        self::assertFalse(Strings::endsWith('this is a test', 'this'));

        self::assertTrue(Strings::endsWith('THIS IS A TEST', 'test', true));
        self::assertFalse(Strings::endsWith('THIS IS A TEST', 'this', true));

        self::assertTrue(Strings::endsWith('THIS IS A TEST', 'test', true, true));
        self::assertFalse(Strings::endsWith('THIS IS A TEST', 'this', true, true));
    }

    /**
     * Test Strings::doesContain().
     */
    public function testDoesContain(): void
    {
        self::assertTrue(Strings::doesContain('start a string', 'a string'));
        self::assertFalse(Strings::doesContain('start a string', 'starting'));

        self::assertTrue(Strings::doesContain('START A STRING', 'a string', true));
        self::assertFalse(Strings::doesContain('START A STRING', 'starting', true));

        self::assertTrue(Strings::doesContain('START A STRING', 'a string', true, true));
        self::assertFalse(Strings::doesContain('START A STRING', 'starting', true, true));
    }

    /**
     * Test Strings::doesNotContain().
     */
    public function testDoesNotContain(): void
    {
        self::assertTrue(Strings::doesNotContain('start a string', 'stringly'));
        self::assertFalse(Strings::doesNotContain('start a string', 'string'));

        self::assertTrue(Strings::doesNotContain('START A STRING', 'stringly', true));
        self::assertFalse(Strings::doesNotContain('START A STRING', 'string', true));

        self::assertTrue(Strings::doesNotContain('START A STRING', 'stringly', true, true));
        self::assertFalse(Strings::doesNotContain('START A STRING', 'string', true, true));
    }

    /**
     * Provides data for testCamelCase().
     *
     * Shoutout to Daniel St. Jules (https://github.com/danielstjules/Stringy/) for
     * inspiration for this function. This function is based on Stringy/Test/camelizeProvider().
     *
     * @return array<int, array<int, string>>
     */
    public static function camelCaseProvider(): array
    {
        return [
            ['camelCase', 'CamelCase'],
            ['camelCase', 'Camel-Case'],
            ['camelCase', 'camel case'],
            ['camelCase', 'camel -case'],
            ['camelCase', 'camel - case'],
            ['camelCase', 'camel_case'],
            ['camelCTest', 'camel c test'],
            ['stringWith1Number', 'string_with1number'],
            ['stringWith22Numbers', 'string-with-2-2 numbers'],
            ['dataRate', 'data_rate'],
            ['backgroundColor', 'background-color'],
            ['yesWeCan', 'yes_we_can'],
            ['mozSomething', '-moz-something'],
            ['carSpeed', '_car_speed_'],
            ['serveHTTP', 'ServeHTTP'],
            ['1Camel2Case', '1camel2case'],
            ['camelΣase', 'camel σase', 'UTF-8'],
            ['στανιλCase', 'Στανιλ case', 'UTF-8'],
            ['σamelCase', 'σamel  Case', 'UTF-8'],
        ];
    }

    /**
     * Test Strings::camelCase().
     */
    #[DataProvider('camelCaseProvider')]
    public function testCamelCase(string $expected, string $string, ?string $encoding = null): void
    {
        if ($encoding !== null) {
            Strings::setEncoding($encoding);
        }

        $result = Strings::camelCase($string);

        self::assertSame($expected, $result);
    }

    /**
     * Test Strings::ascii().
     */
    public function testAscii(): void
    {
        self::assertSame('AA ', Strings::ascii("ǍǺ\xE2\x80\x87"));
    }

    /**
     * Test Strings::ascii().
     */
    public function testAsciiWithLanguage(): void
    {
        self::assertSame('aaistAAIST', Strings::ascii('ăâîșțĂÂÎȘȚ', 'ro'));
    }

    /**
     * Test Strings::slugify().
     */
    public function testSlugify(): void
    {
        self::assertSame('a-simple-title', Strings::slugify('A simple title'));
        self::assertSame('this-post-it-has-a-dash', Strings::slugify('This post -- it has a dash'));
        self::assertSame('123-1251251', Strings::slugify('123----1251251'));

        self::assertSame('a_simple_title', Strings::slugify('A simple title', '_'));
        self::assertSame('this_post_it_has_a_dash', Strings::slugify('This post -- it has a dash', '_'));
        self::assertSame('123_1251251', Strings::slugify('123----1251251', '_'));

        self::assertSame('a-simple-title', Strings::slugify('a-simple-title'));
        self::assertSame('', Strings::slugify(' '));

        self::assertSame('this-is-a-simple-title', Strings::slugify('Țhîș îș ă șîmple țîțle', '-', 'ro'));
    }

    /**
     * Test Strings::randomBytes().
     */
    public function testRandomBytes(): void
    {
        $bytes = Strings::randomBytes(8);
        self::assertNotEmpty($bytes);

        self::expectException(RandomException::class);
        Strings::randomBytes(-10); // @phpstan-ignore-line
    }

    /**
     * Test Strings::randomString().
     */
    public function testRandomString(): void
    {
        $str = Strings::randomString(16);
        self::assertTrue(Strings::length($str) === 16);

        self::expectException(RandomException::class);
        Strings::randomString(-10);
    }

    /**
     * Test Strings::validEmail().
     */
    public function testValidEmail(): void
    {
        self::assertTrue(Strings::validEmail('john.smith@gmail.com'));
        self::assertTrue(Strings::validEmail('john.smith+label@gmail.com'));
        self::assertTrue(Strings::validEmail('john.smith@gmail.co.uk'));
        self::assertFalse(Strings::validEmail('j@'));
    }

    /**
     * Test Strings::validJson().
     */
    public function testValidJson(): void
    {
        self::assertTrue(Strings::validJson('{ "test": { "foo": "bar" } }'));
        self::assertFalse(Strings::validJson('{ "": "": "" } }'));
    }

    /**
     * Test Strings::obscureEmail().
     */
    public function testObscureEmail(): void
    {
        self::assertSame(
            '&#97;&#100;&#109;&#105;&#110;&#64;&#115;&#101;&#99;&#111;&#110;&#100;&#118;&#101;&#114;&#115;&#105;&#111;&#110;&#46;&#99;&#111;&#109;',
            Strings::obscureEmail('admin@secondversion.com')
        );

        self::expectException(InvalidArgumentException::class);
        Strings::obscureEmail('thisisnotvalid&!--');
    }

    /**
     * Test Strings::guid().
     */
    public function testGuid(): void
    {
        $guid = Strings::guid();
        self::assertMatchesRegularExpression('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $guid);
    }
}
