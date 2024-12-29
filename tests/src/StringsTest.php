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

use Esi\Utility\Environment;
use Esi\Utility\Numbers;
use Esi\Utility\Strings;
use InvalidArgumentException;
use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

/**
 * String utility tests.
 *
 * @internal
 *
 * @psalm-api
 */
#[CoversClass(Strings::class)]
#[CoversMethod(Environment::class, 'iniGet')]
#[CoversMethod(Environment::class, 'iniSet')]
#[CoversMethod(Numbers::class, 'random')]
final class StringsTest extends TestCase
{
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
     * Test Strings::getEncoding().
     */
    public function testGetEncoding(): void
    {
        self::assertSame('UTF-8', Strings::getEncoding());
    }

    /**
     * Test Strings::guid().
     */
    public function testGuid(): void
    {
        $guid = Strings::guid();
        self::assertMatchesRegularExpression('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $guid);
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
     * Test Strings::lower().
     */
    public function testLower(): void
    {
        self::assertSame('test', Strings::lower('tESt'));
        self::assertSame('test', Strings::lower('TEST'));
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
     * Test Strings::randomBytes().
     */
    public function testRandomBytes(): void
    {
        $bytes = Strings::randomBytes(8);
        self::assertNotEmpty($bytes);

        self::expectException(RandomException::class);
        Strings::randomBytes(-10);
    }

    /**
     * Test Strings::randomString().
     */
    public function testRandomString(): void
    {
        $str = Strings::randomString(16);
        self::assertSame(16, Strings::length($str));

        self::expectException(RandomException::class);
        Strings::randomString(-10);
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
     * Test Strings::slugify().
     */
    #[DataProvider('slugifyProvider')]
    public function testSlugify(string $expected, string $input, string $separator, string $language): void
    {
        self::assertSame($expected, Strings::slugify($input, $separator, $language));
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
     * Test Strings::substr().
     */
    public function testSubstr(): void
    {
        self::assertSame('f', Strings::substr('abcdef', -1));
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
     * Test Strings::ucfirst().
     */
    public function testUcfirst(): void
    {
        self::assertSame('Test', Strings::ucfirst('test'));
        self::assertSame('TEsT', Strings::ucfirst('tEsT'));
    }

    /**
     * Test Strings::upper().
     */
    public function testUpper(): void
    {
        self::assertSame('TEST', Strings::upper('teSt'));
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
     * Provides data for testCamelCase().
     *
     * Shoutout to Daniel St. Jules (https://github.com/danielstjules/Stringy/). The data for
     * this provider is based on Stringy/Test/camelizeProvider().
     *
     * @see https://github.com/danielstjules/Stringy/blob/master/tests/StringyTest.php#L372
     */
    public static function camelCaseProvider(): Iterator
    {
        yield ['camelCase', 'CamelCase'];
        yield ['camelCase', 'Camel-Case'];
        yield ['camelCase', 'camel case'];
        yield ['camelCase', 'camel -case'];
        yield ['camelCase', 'camel - case'];
        yield ['camelCase', 'camel_case'];
        yield ['camelCTest', 'camel c test'];
        yield ['stringWith1Number', 'string_with1number'];
        yield ['stringWith22Numbers', 'string-with-2-2 numbers'];
        yield ['dataRate', 'data_rate'];
        yield ['backgroundColor', 'background-color'];
        yield ['yesWeCan', 'yes_we_can'];
        yield ['mozSomething', '-moz-something'];
        yield ['carSpeed', '_car_speed_'];
        yield ['serveHTTP', 'ServeHTTP'];
        yield ['1Camel2Case', '1camel2case'];
        yield ['camelΣase', 'camel σase', 'UTF-8'];
        yield ['στανιλCase', 'Στανιλ case', 'UTF-8'];
        yield ['σamelCase', 'σamel  Case', 'UTF-8'];
    }

    public static function slugifyProvider(): Iterator
    {
        yield ['a-simple-title', 'A simple title', '-', 'en'];
        yield ['this-post-it-has-a-dash', 'This post -- it has a dash', '-', 'en'];
        yield ['123-1251251', '123----1251251', '-', 'en'];
        yield ['a_simple_title', 'A simple title', '_', 'en'];
        yield ['this_post_it_has_a_dash', 'This post -- it has a dash', '_', 'en'];
        yield ['123_1251251', '123----1251251', '_', 'en'];
        yield ['a-simple-title', 'a-simple-title', '-', 'en'];
        yield ['', ' ', '-', 'en'];
        yield ['this-is-a-simple-title', 'Țhîș îș ă șîmple țîțle', '-', 'ro'];
    }
}
