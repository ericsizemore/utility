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

namespace Esi\Utility\Tests\Fixtures;

use ArrayAccess;

/**
 * @implements ArrayAccess<mixed, mixed>
 */
class TestArrayAccess implements ArrayAccess
{
    /**
     * @var array<int|string, mixed>
     */
    public array $container = [
        'one'   => 1,
        'two'   => 2,
        'three' => 3,
    ];

    /**
     * Whether an offset exists.
     */
    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Retrieve an offset exists.
     */
    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Set an offset.
     */
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (\is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unset an offset.
     */
    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        unset($this->container[$offset]);
    }
}
