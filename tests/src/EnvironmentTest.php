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

use ArgumentCountError;
use Esi\Utility\Arrays;
use Esi\Utility\Environment;
use Esi\Utility\Strings;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function sprintf;

/**
 * Environment utility tests.
 *
 * @internal
 */
#[CoversClass(Environment::class)]
#[CoversMethod(Arrays::class, 'get')]
#[CoversMethod(Arrays::class, 'keyExists')]
#[CoversMethod(Arrays::class, 'mapDeep')]
#[CoversMethod(Arrays::class, 'set')]
#[CoversMethod(Strings::class, 'length')]
#[CoversMethod(Strings::class, 'lower')]
#[CoversMethod(Strings::class, 'upper')]
class EnvironmentTest extends TestCase
{
    /**
     * Test Environment::host().
     */
    public function testHost(): void
    {
        $origHost    = Environment::var('HTTP_HOST');
        $origFwdHost = Environment::var('HTTP_X_FORWARDED_HOST');
        $origSrvName = Environment::var('SERVER_NAME');

        Arrays::set($_SERVER, 'HTTP_HOST', 'example.com');
        Arrays::set($_SERVER, 'HTTP_X_FORWARDED_HOST', 'example2.com');
        Arrays::set($_SERVER, 'SERVER_NAME', null);

        self::assertSame('example2.com', Environment::host(false, true));
        self::assertSame('example.com', Environment::host());

        Arrays::set($_SERVER, 'HTTP_HOST', 'www.example.com');

        self::assertSame('www.example.com', Environment::host());
        self::assertSame('example.com', Environment::host(true));

        Arrays::set($_SERVER, 'HTTP_HOST', null);
        Arrays::set($_SERVER, 'SERVER_NAME', null);
        self::assertSame('localhost', Environment::host());

        Arrays::set($_SERVER, 'HTTP_HOST', $origHost);
        Arrays::set($_SERVER, 'HTTP_X_FORWARDED_HOST', $origFwdHost);
        Arrays::set($_SERVER, 'SERVER_NAME', $origSrvName);
    }

    /**
     * Test Environment::iniGet().
     */
    public function testIniGet(): void
    {
        self::assertNotEmpty(Environment::iniGet('request_order'));

        self::assertLessThanOrEqual('1', Environment::iniGet('display_errors', true));

        self::expectException(RuntimeException::class);
        Environment::iniGet('');

        self::expectException(RuntimeException::class);
        Environment::iniGet('this_should_notbe_a_valid_option');
    }

    /**
     * Test Environment::iniSet().
     */
    public function testIniSet(): void
    {
        // @var string $oldValue
        $oldValue = Environment::iniSet('display_errors', Environment::iniGet('display_errors'));

        self::assertSame($oldValue, Environment::iniSet('display_errors', $oldValue));

        self::expectException(ArgumentCountError::class);
        // @phpstan-ignore-next-line
        Environment::iniSet('');

        self::expectException(InvalidArgumentException::class);
        Environment::iniSet('', '');
    }

    /**
     * Test Environment::ipAddress().
     */
    public function testIpAddress(): void
    {
        Arrays::set($_SERVER, 'REMOTE_ADDR', '1.1.1.1');
        Arrays::set($_SERVER, 'HTTP_CLIENT_IP', '1.1.1.2');
        Arrays::set($_SERVER, 'HTTP_X_FORWARDED_FOR', '1.1.1.3');
        Arrays::set($_SERVER, 'HTTP_X_REAL_IP', '1.1.1.4');

        self::assertSame('1.1.1.1', Environment::ipAddress());
        self::assertSame('1.1.1.3', Environment::ipAddress(true));

        Arrays::set($_SERVER, 'HTTP_X_FORWARDED_FOR', null);

        self::assertSame('1.1.1.4', Environment::ipAddress(true));

        Arrays::set($_SERVER, 'HTTP_X_REAL_IP', null);

        self::assertSame('1.1.1.2', Environment::ipAddress(true));

        // What if from cloudflare?
        Arrays::set($_SERVER, 'HTTP_CF_CONNECTING_IP', '1.1.1.5');

        self::assertSame('1.1.1.5', Environment::ipAddress());
        self::assertSame('1.1.1.2', Environment::ipAddress(true));

        Arrays::set($_SERVER, 'HTTP_CF_CONNECTING_IP', null);
    }

    /**
     * Test Environment::isHttps().
     */
    public function testIsHttps(): void
    {
        Arrays::set($_SERVER, 'HTTPS', null);

        self::assertFalse(Environment::isHttps());

        Arrays::set($_SERVER, 'HTTPS', 'on');

        self::assertTrue(Environment::isHttps());

        Arrays::set($_SERVER, 'HTTPS', null);

        Arrays::set($_SERVER, 'HTTP_X_FORWARDED_PROTO', 'https');
        self::assertTrue(Environment::isHttps());

        Arrays::set($_SERVER, 'HTTP_X_FORWARDED_PROTO', null);
    }

    /**
     * Test Environment::isPrivateIp().
     */
    public function testIsPrivateIp(): void
    {
        self::assertTrue(Environment::isPrivateIp('192.168.0.0'));
        self::assertFalse(Environment::isPrivateIp('1.1.1.1'));
    }

    /**
     * Test Environment::isPublicIp().
     */
    public function testIsPublicIp(): void
    {
        self::assertTrue(Environment::isPublicIp('1.1.1.1'));
        self::assertFalse(Environment::isPublicIp('192.168.0.0'));
        self::assertFalse(Environment::isPublicIp('0.255.255.255'));
    }

    /**
     * Test Environment::isReservedIp().
     */
    public function testIsReservedIp(): void
    {
        self::assertTrue(Environment::isReservedIp('0.255.255.255'));
        self::assertFalse(Environment::isReservedIp('192.168.0.0'));
    }

    /**
     * Test Environment::requestMethod().
     */
    public function testRequestMethod(): void
    {
        Arrays::set($_SERVER, 'HTTP_X_HTTP_METHOD_OVERRIDE', 'GET');
        Arrays::set($_SERVER, 'REQUEST_METHOD', 'POST');

        self::assertSame('GET', Environment::requestMethod());

        Arrays::set($_SERVER, 'HTTP_X_HTTP_METHOD_OVERRIDE', null);

        self::assertSame('POST', Environment::requestMethod());

        Arrays::set($_SERVER, 'HTTP_X_HTTP_METHOD_OVERRIDE', 'GET');
        Arrays::set($_SERVER, 'REQUEST_METHOD', null);

        self::assertSame('GET', Environment::requestMethod());
    }

    /**
     * Test Environment::url().
     */
    public function testUrl(): void
    {
        $expected      = 'http://test.dev/test.php?foo=bar';
        $expectedAuth  = 'http://admin:123@test.dev/test.php?foo=bar';
        $expectedPort  = sprintf('http://test.dev:%d/test.php?foo=bar', Environment::PORT_SECURE);
        $expectedPort2 = sprintf('https://test.dev:%d/test.php?foo=bar', Environment::PORT_UNSECURE);
        $expectedSSL   = 'https://test.dev/test.php?foo=bar';

        Arrays::set($_SERVER, 'HTTP_HOST', 'test.dev');
        Arrays::set($_SERVER, 'REQUEST_URI', '/test.php?foo=bar');
        Arrays::set($_SERVER, 'QUERY_STRING', 'foo=bar');
        Arrays::set($_SERVER, 'PHP_SELF', '/test.php');

        // Test basic url.
        self::assertSame($expected, Environment::url());

        // Test server auth.
        Arrays::set($_SERVER, 'PHP_AUTH_USER', 'admin');
        Arrays::set($_SERVER, 'PHP_AUTH_PW', '123');
        self::assertSame($expectedAuth, Environment::url());

        Arrays::set($_SERVER, 'PHP_AUTH_USER', null);
        Arrays::set($_SERVER, 'PHP_AUTH_PW', null);

        // Test port.
        Arrays::set($_SERVER, 'SERVER_PORT', Environment::PORT_SECURE);
        self::assertSame($expectedPort, Environment::url());

        // Test SSL.
        Arrays::set($_SERVER, 'HTTPS', 'on');
        self::assertSame($expectedSSL, Environment::url());

        Arrays::set($_SERVER, 'SERVER_PORT', Environment::PORT_UNSECURE);
        self::assertSame($expectedPort2, Environment::url());

        Arrays::set($_SERVER, 'HTTPS', null);

        // Test no $_SERVER['REQUEST_URI'] (e.g., MS IIS).
        Arrays::set($_SERVER, 'REQUEST_URI', null);
        self::assertSame($expected, Environment::url());

        // Reset.
        Arrays::set($_SERVER, 'HTTP_HOST', null);
        Arrays::set($_SERVER, 'QUERY_STRING', null);
        Arrays::set($_SERVER, 'PHP_SELF', null);
    }
}
