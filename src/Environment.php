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

namespace Esi\Utility;

use ArgumentCountError;
use RuntimeException;

use function explode;
use function filter_var;
use function getallheaders;
use function ini_set;
use function preg_match;
use function preg_replace;
use function trim;

use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_FLAG_NO_PRIV_RANGE;
use const FILTER_FLAG_NO_RES_RANGE;
use const FILTER_VALIDATE_IP;

/**
 * Environment utilities.
 *
 * @see Tests\EnvironmentTest
 */
abstract class Environment
{
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
     * Default https/http port numbers.
     *
     * @var int PORT_SECURE
     * @var int PORT_UNSECURE
     */
    public const PORT_SECURE = 443;

    public const PORT_UNSECURE = 80;

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
     * Regex used by Environment::host() to validate a hostname.
     *
     * @var string VALIDATE_HOST_REGEX
     */
    public const VALIDATE_HOST_REGEX = '#^\[?(?:[a-z0-9-:\]_]+\.?)+$#';

    private static ?bool $iniGetAvailable = null;

    private static ?bool $iniSetAvailable = null;

    /**
     * host().
     *
     * Determines current hostname.
     *
     * @param bool $stripWww        True to strip www. off the host, false to leave it be.
     * @param bool $acceptForwarded True to accept, false otherwise.
     *
     * @return non-empty-string
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
            return self::HOST_HEADERS['default'];
        }

        $host = Strings::lower($host);

        if ($stripWww) {
            /** @var non-empty-string $result */
            $result = (string) preg_replace('#^www\.#', '', $host);

            return $result;
        }

        /** @var non-empty-string */
        return $host;
    }

    /**
     * iniGet().
     *
     * Safe ini_get taking into account its availability.
     *
     * @param string $option      The configuration option name.
     * @param bool   $standardize Standardize returned values to 1 or 0?
     *
     * @throws ArgumentCountError|RuntimeException
     */
    public static function iniGet(string $option, bool $standardize = false): string
    {
        self::$iniGetAvailable ??= \function_exists('ini_get');

        if (self::$iniGetAvailable === false) {
            //@codeCoverageIgnoreStart
            throw new RuntimeException('Native ini_get function not available.');
            //@codeCoverageIgnoreEnd
        }

        $value = \ini_get($option);

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
     * @param null|bool|float|int|string $value  The new value for the option.
     *
     * @throws ArgumentCountError|RuntimeException
     *
     * @return false|string
     */
    public static function iniSet(string $option, null|bool|float|int|string $value): false|string
    {
        self::$iniSetAvailable ??= \function_exists('ini_set');

        if (self::$iniSetAvailable === false) {
            //@codeCoverageIgnoreStart
            throw new RuntimeException('Native ini_set function not available.');
            //@codeCoverageIgnoreEnd
        }

        return ini_set($option, (string) $value);
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
            /** @var array<string, mixed> $_SERVER */
            Arrays::set($_SERVER, self::IP_ADDRESS_HEADERS['default'], $cloudflare);
        }

        // If we are not trusting HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR, we return REMOTE_ADDR.
        if (!$trustProxy) {
            /** @var string */
            return Environment::var(self::IP_ADDRESS_HEADERS['default']);
        }

        $ip = '';

        /** @var string */
        $forwarded = Environment::var(self::IP_ADDRESS_HEADERS['forwarded']);
        /** @var string */
        $realip = Environment::var(self::IP_ADDRESS_HEADERS['realip']);

        $ips = match (true) {
            $forwarded !== '' => explode(',', $forwarded),
            $realip !== ''    => explode(',', $realip),
            default           => []
        };

        /** @var list<string> $mappedIps */
        $mappedIps = Arrays::mapDeep($ips, static fn (mixed $value): string => \is_string($value) ? trim($value) : '');

        $filteredIps = array_filter(
            $mappedIps,
            static fn (string $value): bool => Strings::length($value) > 0
        );

        foreach ($filteredIps as $value) {
            if (Environment::isPublicIp($value)) {
                $ip = $value;
                break;
            }
        }

        // If at this point $ip is empty, then we are not dealing with proxy ip's
        if ($ip === '') {
            /** @var string */
            $ip = Environment::var(
                self::IP_ADDRESS_HEADERS['client'],
                Environment::var(self::IP_ADDRESS_HEADERS['default'])
            );
        }

        return $ip;
    }

    /**
     * isHttps().
     *
     * Checks to see if SSL is in use.
     */
    public static function isHttps(): bool
    {
        /** @var array<string, string> $headers */
        $headers = getallheaders();

        /** @var string $server */
        $server    = Environment::var(self::HTTPS_HEADERS['default']);
        $frontEnd  = Arrays::get($headers, self::HTTPS_HEADERS['forwarded'], '');
        $forwarded = Arrays::get($headers, self::HTTPS_HEADERS['frontend'], '');

        if ($server !== 'off' && $server !== '') {
            return true;
        }

        return $forwarded === 'https' || ($frontEnd !== '' && $frontEnd !== 'off');
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
     * url().
     *
     * Retrieve the current URL.
     */
    public static function url(): string
    {
        $scheme = (Environment::isHttps()) ? 'https://' : 'http://';

        $authUser = (string) Environment::var(self::URL_HEADERS['authuser']);
        $authPwd  = (string) Environment::var(self::URL_HEADERS['authpw']);

        $auth = ($authUser !== '' || $authPwd !== '')
            ? \sprintf('%s:%s@', $authUser, $authPwd)
            : '';

        $host = Environment::host();
        $port = (int) Environment::var(self::URL_HEADERS['port'], 0);

        $portStr = ($port === (Environment::isHttps() ? Environment::PORT_SECURE : Environment::PORT_UNSECURE)
            || $port === 0) ? '' : ':' . $port;

        /** @var string $self */
        $self = Environment::var(self::URL_HEADERS['self']);
        /** @var string $query */
        $query = Environment::var(self::URL_HEADERS['query']);
        /** @var string $request */
        $request = Environment::var(self::URL_HEADERS['request']);

        $path = ($request === '' ? $self . ($query !== '' ? '?' . $query : '') : $request);

        /** @var non-empty-string */
        return \sprintf('%s%s%s%s%s', $scheme, $auth, $host, $portStr, $path);
    }

    /**
     * var().
     *
     * Gets a variable from $_SERVER using $default if not provided.
     *
     * @param string          $var     Variable name.
     * @param null|int|string $default Default value to substitute.
     */
    public static function var(string $var, null|int|string $default = ''): null|int|string
    {
        /** @var null|int|string $value */
        $value = Arrays::get($_SERVER, $var) ?? $default;

        return $value;
    }
}
