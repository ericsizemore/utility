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
use RuntimeException;
use ArgumentCountError;

// Functions
use function explode;
use function count;
use function inet_ntop;
use function inet_pton;
use function filter_var;
use function trim;
use function preg_match;
use function preg_replace;
use function sprintf;
use function ini_get;
use function ini_set;
use function function_exists;

// Constants
use const FILTER_VALIDATE_IP;
use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_FLAG_NO_PRIV_RANGE;
use const FILTER_FLAG_NO_RES_RANGE;

/**
 * Environment utilities.
 */
final class Environment
{
    /**
     * requestMethod()
     *
     * Gets the request method.
     *
     * @return  string
     */
    public static function requestMethod(): string
    {
        /** @var string $method */
        $method = (
            Environment::var('HTTP_X_HTTP_METHOD_OVERRIDE', Environment::var('REQUEST_METHOD', 'GET'))
        );
        return Strings::upper($method);
    }

    /**
     * var()
     *
     * Gets a variable from $_SERVER using $default if not provided.
     *
     * @param   string           $var      Variable name.
     * @param   string|int|null  $default  Default value to substitute.
     * @return  string|int|null
     */
    public static function var(string $var, string | int | null $default = ''): string | int | null
    {
        /** @var string|int|null $value */
        $value = Arrays::get($_SERVER, $var) ?? $default;

        return $value;
    }

    /**
     * ipAddress()
     *
     * Return the visitor's IP address.
     *
     * @param   bool    $trustProxy  Whether to trust HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR.
     * @return  string
     */
    public static function ipAddress(bool $trustProxy = false): string
    {
        // Pretty self-explanatory. Try to get an 'accurate' IP
        $cloudflare = Environment::var('HTTP_CF_CONNECTING_IP');

        if ($cloudflare !== '') {
            Arrays::set($_SERVER, 'REMOTE_ADDR', $cloudflare);
        }

        if (!$trustProxy) {
            /** @var string */
            return Environment::var('REMOTE_ADDR');
        }

        $ip = '';
        $ips = [];

        /** @var string $forwarded */
        $forwarded = Environment::var('HTTP_X_FORWARDED_FOR');
        /** @var string $realip */
        $realip = Environment::var('HTTP_X_REAL_IP');

        if ($forwarded !== '') {
            /** @var list<string> $ips */
            $ips = explode(',', $forwarded);
        } elseif ($realip !== '') {
            /** @var list<string> $ips */
            $ips = explode(',', $realip);
        }

        /** @var list<string> $ips */
        $ips = Arrays::mapDeep($ips, 'trim');

        if (count($ips) > 0) {
            foreach ($ips as $val) {
                // @phpstan-ignore-next-line
                if (inet_ntop(inet_pton($val)) === $val && Environment::isPublicIp($val)) {
                    /** @var string $ip */
                    $ip = $val;
                    break;
                }
            }
        }
        unset($ips);

        if ($ip === '') {
            /** @var string $ip */
            $ip = Environment::var('HTTP_CLIENT_IP', Environment::var('REMOTE_ADDR'));
        }
        return $ip;
    }

    /**
     * isPrivateIp()
     *
     * Determines if an IP address is within the private range.
     *
     * @param   string  $ipaddress  IP address to check.
     * @return  bool
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
     * isReservedIp()
     *
     * Determines if an IP address is within the reserved range.
     *
     * @param   string  $ipaddress  IP address to check.
     * @return  bool
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
     * isPublicIp()
     *
     * Determines if an IP address is not within the private or reserved ranges.
     *
     * @param   string  $ipaddress  IP address to check.
     * @return  bool
     */
    public static function isPublicIp(string $ipaddress): bool
    {
        return (!Environment::isPrivateIp($ipaddress) && !Environment::isReservedIp($ipaddress));
    }

    /**
     * host()
     *
     * Determines current hostname.
     *
     * @param   bool    $stripWww         True to strip www. off the host, false to leave it be.
     * @param   bool    $acceptForwarded  True to accept, false otherwise.
     * @return  string
     */
    public static function host(bool $stripWww = false, bool $acceptForwarded = false): string
    {
        /** @var string $forwarded */
        $forwarded = Environment::var('HTTP_X_FORWARDED_HOST');

        /** @var string $host */
        $host = (($acceptForwarded && ($forwarded !== '')) ? $forwarded : (Environment::var('HTTP_HOST', Environment::var('SERVER_NAME'))));
        $host = trim($host);

        if ($host === '' || preg_match('#^\[?(?:[a-z0-9-:\]_]+\.?)+$#', $host) === 0) {
            $host = 'localhost';
        }

        $host = Strings::lower($host);

        // Strip 'www.'
        if ($stripWww) {
            $strippedHost = preg_replace('#^www\.#', '', $host);
        }
        return ($strippedHost ?? $host);
    }

    /**
     * isHttps()
     *
     * Checks to see if SSL is in use.
     *
     * @return  bool
     */
    public static function isHttps(): bool
    {
        $headers = \getallheaders();

        $server = Environment::var('HTTPS');
        $frontEnd = Arrays::get($headers, 'X-Forwarded-Proto', '');
        $forwarded = Arrays::get($headers, 'Front-End-Https', '');

        if ($server !== 'off' && $server !== '') {
            return true;
        }
        return $forwarded === 'https' || ($frontEnd !== '' && $frontEnd !== 'off');
    }

    /**
     * url()
     *
     * Retrieve the current URL.
     *
     * @return  string
     */
    public static function url(): string
    {
        // Scheme
        $scheme = (Environment::isHttps()) ? 'https://' : 'http://';

        // Auth
        $authUser = Environment::var('PHP_AUTH_USER');
        $authPwd = Environment::var('PHP_AUTH_PW');
        $auth = ($authUser !== '' ? $authUser . ($authPwd !== '' ? ":$authPwd" : '') . '@' : '');

        // Host and port
        $host = Environment::host();

        /** @var int $port */
        $port = Environment::var('SERVER_PORT', 0);
        $port = ($port === (Environment::isHttps() ? 443 : 80) || $port === 0) ? '' : ":$port";

        // Path
        /** @var string $self */
        $self = Environment::var('PHP_SELF');
        /** @var string $query */
        $query = Environment::var('QUERY_STRING');
        /** @var string $request */
        $request = Environment::var('REQUEST_URI');
        /** @var string $path */
        $path = ($request === '' ? $self . ($query !== '' ? '?' . $query : '') : $request);

        // Put it all together
        /** @var non-falsy-string $url */
        $url = sprintf('%s%s%s%s%s', $scheme, $auth, $host, $port, $path);

        return $url;
    }

    /**
     * iniGet()
     *
     * Safe ini_get taking into account its availability.
     *
     * @param   string  $option       The configuration option name.
     * @param   bool    $standardize  Standardize returned values to 1 or 0?
     * @return  string
     *
     * @throws  RuntimeException|ArgumentCountError
     */
    public static function iniGet(string $option, bool $standardize = false): string
    {
        // @codeCoverageIgnoreStart
        if (!function_exists('ini_get')) {
            // disabled_functions?
            throw new RuntimeException('Native ini_get function not available.');
        }
        // @codeCoverageIgnoreEnd
        $value = ini_get($option);

        if ($value === false) {
            throw new RuntimeException('$option does not exist.');
        }

        $value = trim($value);

        if ($standardize) {
            return match (Strings::lower($value)) {
                'yes', 'on', 'true', '1' => '1',
                'no', 'off', 'false', '0' => '0',
                default => $value
            };
        }
        return $value;
    }

    /**
     * iniSet()
     *
     * Safe ini_set taking into account its availability.
     *
     * @param   string  $option  The configuration option name.
     * @param   string|int|float|bool|null $value   The new value for the option.
     * @return  string|false
     *
     * @throws RuntimeException|ArgumentCountError
     */
    public static function iniSet(string $option, string|int|float|bool|null $value): string | false
    {
        // @codeCoverageIgnoreStart
        if (!function_exists('ini_set')) {
            // disabled_functions?
            throw new RuntimeException('Native ini_set function not available.');
        }
        // @codeCoverageIgnoreEnd
        return ini_set($option, $value);
    }
}
