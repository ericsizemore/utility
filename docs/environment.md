# Environment

`Esi\Utility\Environment`

* [requestMethod](#requestmethod)(): string;
* [var](#var)(string $var, string | int | null $default = ''): string | int | null;
* [ipAddress](#ipaddress)(bool $trustProxy = false): string;
* [isPrivateIp](#isprivateip)(string $ipaddress): bool;
* [isReservedIp](#isreservedip)(string $ipaddress): bool;
* [isPublicIp](#ispublicip)(string $ipaddress): bool;
* [host](#host)(bool $stripWww = false, bool $acceptForwarded = false): string;
* [isHttps](#ishttps)(): bool;
* [url](#url)(): string;
* [iniGet](#iniget)(string $option, bool $standardize = false): string | false;
* [iniSet](#iniset)(string $option, string $value): string | false;


## 



```php

```
