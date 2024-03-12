<?php

declare(strict_types=1);

/**
 * Utility - Collection of various PHP utility functions.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 *
 * @version   2.0.0
 *
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
use ArgumentCountError;
use RuntimeException;

// Functions

use function explode;
use function filter_var;
use function function_exists;
use function getallheaders;
use function ini_get;
use function ini_set;
use function preg_match;
use function preg_replace;
use function sprintf;

// Constants
use function trim;

use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_FLAG_NO_PRIV_RANGE;
use const FILTER_FLAG_NO_RES_RANGE;
use const FILTER_VALIDATE_IP;

/**
 * Environment utilities.
 *
 * @see \Esi\Utility\Tests\EnvironmentTest
 */
final class Environment
{
    /**
     * Default https/http port numbers.
     *
     * @var int PORT_SECURE
     * @var int PORT_UNSECURE
     */
    public const PORT_SECURE = 443;

    public const PORT_UNSECURE = 80;

    /**
     * Regex used by Environment::host() to validate a hostname.
     *
     * @var string VALIDATE_HOST_REGEX
     */
    public const VALIDATE_HOST_REGEX = '#^\[?(?:[a-z0-9-:\]_]+\.?)+$#';

    /**
     * Maps values to their boolean equivalent for Environment::iniGet(standardize: true).
     *
     * @var array<string> BOOLEAN_MAPPINGS
     */
    public const BOOLEAN_MAPPINGS = [
        'yes'   => '1',
        'on'    => '1',
        'true'  => '1',
        '1'     => '1',
        'no'    => '0',
        'off'   => '0',
        'false' => '0',
        '0'     => '0',
    ];

    /**
     * The default list of headers that Environment::getIpAddress() checks for.
     *
     * @var array<string> IP_ADDRESS_HEADERS
     */
    public const IP_ADDRESS_HEADERS = [
        'cloudflare' => 'HTTP_CF_CONNECTING_IP',
        'forwarded'  => 'HTTP_X_FORWARDED_FOR',
        'realip'     => 'HTTP_X_REAL_IP',
        'client'     => 'HTTP_CLIENT_IP',
        'default'    => 'REMOTE_ADDR',
    ];

    /**
     * A list of headers that Environment::host() checks to determine hostname, with a default of 'localhost'
     * if it cannot make a determination.
     *
     * @var array<string> HOST_HEADERS
     */
    public const HOST_HEADERS = [
        'forwarded' => 'HTTP_X_FORWARDED_HOST',
        'server'    => 'SERVER_NAME',
        'host'      => 'HTTP_HOST',
        'default'   => 'localhost',
    ];

    /**
     * A list of headers that Environment::url() checks for and uses to build a URL.
     *
     * @var array<string> URL_HEADERS
     */
    public const URL_HEADERS = [
        'authuser' => 'PHP_AUTH_USER',
        'authpw'   => 'PHP_AUTH_PW',
        'port'     => 'SERVER_PORT',
        'self'     => 'PHP_SELF',
        'query'    => 'QUERY_STRING',
        'request'  => 'REQUEST_URI',
    ];

    /**
     * A list of headers that Environment::isHttps() checks for to determine if current
     * environment is under SSL.
     *
     * @var array<string> HTTPS_HEADERS
     */
    public const HTTPS_HEADERS = [
        'default'   => 'HTTPS',
        'forwarded' => 'X-Forwarded-Proto',
        'frontend'  => 'Front-End-Https',
    ];

    /**
     * A list of options/headers used by Environment::requestMethod() to determine
     * current request method.
     *
     * @var array<string> REQUEST_HEADERS
     */
    public const REQUEST_HEADERS = [
        'override' => 'HTTP_X_HTTP_METHOD_OVERRIDE',
        'method'   => 'REQUEST_METHOD',
        'default'  => 'GET',
    ];

    /**
     * requestMethod().
     *
     * Gets the request method.
     */
    public static function requestMethod(): string
    {
        /** @var string $method */
        $method = (
            Environment::var(
                self::REQUEST_HEADERS['override'],
                Environment::var(
                    self::REQUEST_HEADERS['method'],
                    self::REQUEST_HEADERS['default']
                )
            )
        );

        return Strings::upper($method);
    }

    /**
     * var().
     *
     * Gets a variable from $_SERVER using $default if not provided.
     *
     * @param string          $var     Variable name.
     * @param string|int|null $default Default value to substitute.
     */
    public static function var(string $var, string | int | null $default = ''): string | int | null
    {
        /** @var string|int|null $value */
        $value = Arrays::get($_SERVER, $var) ?? $default;

        return $value;
    }

    /**
     * ipAddress().
     *
     * Return the visitor's IP address.
     *
     * @param bool $trustProxy Whether to trust HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR.
     */
    public static function ipAddress(bool $trustProxy = false): string
    {
        // If behind cloudflare, attempt to grab the IP forwarded from the service.
        $cloudflare = Environment::var(self::IP_ADDRESS_HEADERS['cloudflare']);

        // cloudflare connecting ip found, update REMOTE_ADDR
        if ($cloudflare !== '') {
            Arrays::set($_SERVER, self::IP_ADDRESS_HEADERS['default'], $cloudflare);
        }

        // If we are not trusting HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR, we return REMOTE_ADDR.
        if (!$trustProxy) {
            /** @var string */
            return Environment::var(self::IP_ADDRESS_HEADERS['default']);
        }

        $ip  = '';
        $ips = [];

        /** @var string $forwarded */
        $forwarded = Environment::var(self::IP_ADDRESS_HEADERS['forwarded']);

        /** @var string $realip */
        $realip = Environment::var(self::IP_ADDRESS_HEADERS['realip']);

        if ($forwarded !== '') {
            /** @var list<string> $ips */
            $ips = explode(',', $forwarded);
        } elseif ($realip !== '') {
            /** @var list<string> $ips */
            $ips = explode(',', $realip);
        }

        /** @var list<string> $ips */
        $ips = Arrays::mapDeep($ips, 'trim');

        // Filter out any potentially empty entries
        $ips = array_filter($ips, static fn (string $string): bool => Strings::length($string) > 0);

        // Traverses the $ips array. Set $ip to current value if it's a public IP.
        array_walk($ips, static function (string $value, int $key) use (&$ip): string {
            if (Environment::isPublicIp($value)) {
                $ip = $value;
            }

            return $ip;
        });
        unset($ips);

        // If at this point $ip is empty, then we are not dealing with proxy ip's
        if ($ip === '') {
            /** @var string $ip */
            $ip = Environment::var(
                self::IP_ADDRESS_HEADERS['client'],
                Environment::var(self::IP_ADDRESS_HEADERS['default'])
            );
        }

        return $ip;
    }

    /**
     * isPrivateIp().
     *
     * Determines if an IP address is within the private range.
     *
     * @param string $ipaddress IP address to check.
     */
    public static function isPrivateIp(string $ipaddress): bool
    {
        return !(bool) filter_var(
            $ipaddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE
        );
    }

    /**
     * isReservedIp().
     *
     * Determines if an IP address is within the reserved range.
     *
     * @param string $ipaddress IP address to check.
     */
    public static function isReservedIp(string $ipaddress): bool
    {
        return !(bool) filter_var(
            $ipaddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * isPublicIp().
     *
     * Determines if an IP address is not within the private or reserved ranges.
     *
     * @param string $ipaddress IP address to check.
     */
    public static function isPublicIp(string $ipaddress): bool
    {
        return (!Environment::isPrivateIp($ipaddress) && !Environment::isReservedIp($ipaddress));
    }

    /**
     * host().
     *
     * Determines current hostname.
     *
     * @param bool $stripWww        True to strip www. off the host, false to leave it be.
     * @param bool $acceptForwarded True to accept, false otherwise.
     */
    public static function host(bool $stripWww = false, bool $acceptForwarded = false): string
    {
        /** @var string $forwarded */
        $forwarded = Environment::var(self::HOST_HEADERS['forwarded']);

        /** @var string $host */
        $host = (
            ($acceptForwarded && ($forwarded !== ''))
            ? $forwarded
            : (Environment::var(self::HOST_HEADERS['host'], Environment::var(self::HOST_HEADERS['server'])))
        );
        $host = trim($host);

        if ($host === '' || preg_match(Environment::VALIDATE_HOST_REGEX, $host) === 0) {
            $host = self::HOST_HEADERS['default'];
        }

        $host = Strings::lower($host);

        // Strip 'www.'
        if ($stripWww) {
            $strippedHost = preg_replace('#^www\.#', '', $host);
        }

        return ($strippedHost ?? $host);
    }

    /**
     * isHttps().
     *
     * Checks to see if SSL is in use.
     */
    public static function isHttps(): bool
    {
        $headers = getallheaders();

        $server    = Environment::var(self::HTTPS_HEADERS['default']);
        $frontEnd  = Arrays::get($headers, self::HTTPS_HEADERS['forwarded'], '');
        $forwarded = Arrays::get($headers, self::HTTPS_HEADERS['frontend'], '');

        if ($server !== 'off' && $server !== '') {
            return true;
        }

        return $forwarded === 'https' || ($frontEnd !== '' && $frontEnd !== 'off');
    }

    /**
     * url().
     *
     * Retrieve the current URL.
     */
    public static function url(): string
    {
        // Scheme
        $scheme = (Environment::isHttps()) ? 'https://' : 'http://';

        // Auth
        $authUser = Environment::var(self::URL_HEADERS['authuser']);
        $authPwd  = Environment::var(self::URL_HEADERS['authpw']);
        $auth     = ($authUser !== '' ? $authUser . ($authPwd !== '' ? ':' . $authPwd : '') . '@' : '');

        // Host and port
        $host = Environment::host();

        /** @var int $port */
        $port = Environment::var(self::URL_HEADERS['port'], 0);
        $port = ($port === (Environment::isHttps() ? Environment::PORT_SECURE : Environment::PORT_UNSECURE) || $port === 0) ? '' : ':' . $port;

        // Path
        /** @var string $self */
        $self = Environment::var(self::URL_HEADERS['self']);

        /** @var string $query */
        $query = Environment::var(self::URL_HEADERS['query']);

        /** @var string $request */
        $request = Environment::var(self::URL_HEADERS['request']);

        /** @var string $path */
        $path = ($request === '' ? $self . ($query !== '' ? '?' . $query : '') : $request);

        // Put it all together
        /** @var non-falsy-string $url */
        $url = sprintf('%s%s%s%s%s', $scheme, $auth, $host, $port, $path);

        return $url;
    }

    /**
     * iniGet().
     *
     * Safe ini_get taking into account its availability.
     *
     * @param string $option      The configuration option name.
     * @param bool   $standardize Standardize returned values to 1 or 0?
     *
     * @throws RuntimeException|ArgumentCountError
     */
    public static function iniGet(string $option, bool $standardize = false): string
    {
        static $iniGetAvailable;

        $iniGetAvailable ??= function_exists('ini_get');

        if (!$iniGetAvailable) {
            // disabled_functions?
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Native ini_get function not available.');
            // @codeCoverageIgnoreEnd
        }

        $value = ini_get($option);

        if ($value === false) {
            throw new RuntimeException('$option does not exist.');
        }

        $value = trim($value);

        if ($standardize) {
            return Environment::BOOLEAN_MAPPINGS[Strings::lower($value)] ?? $value;
        }

        return $value;
    }

    /**
     * iniSet().
     *
     * Safe ini_set taking into account its availability.
     *
     * @param string                     $option The configuration option name.
     * @param string|int|float|bool|null $value  The new value for the option.
     *
     * @return string|false
     *
     * @throws RuntimeException|ArgumentCountError
     */
    public static function iniSet(string $option, string | int | float | bool | null $value): string | false
    {
        static $iniSetAvailable;

        $iniSetAvailable ??= function_exists('ini_set');

        if (!$iniSetAvailable) {
            // disabled_functions?
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Native ini_set function not available.');
            // @codeCoverageIgnoreEnd
        }

        return ini_set($option, $value);
    }
}
