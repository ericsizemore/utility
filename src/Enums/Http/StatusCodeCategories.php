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

/**
 * Esi\Utility\Enums\Http is a fork of Crell/EnumTools (https://github.com/Crell/EnumTools)
 * which is also licensed under the MIT license.
 *      Crell/EnumTools Copyright 2021 Larry Garfield<larry@garfieldtech.com>
 *
 * For changes made in Esi\Utility\Enums\Http compared to the original Crell/EnumTools, see CHANGELOG.md#2.0.0
 */

namespace Esi\Utility\Enums\Http;

/**
 * Enum of the types/categories of HTTP Status Codes.
 *
 * @since 2.0.0
 */
enum StatusCodeCategories: string
{
    case Informational = 'Informational';
    case Success = 'Success';
    case Redirection = 'Redirection';
    case ClientError = 'Client Error';
    case ServerError = 'Server Error';
    case Unknown = 'Unknown';

    /**
     * Returns the value of a given case.
     *  eg: Methods::Redirection->getValue() // Redirection
     *
     * @return string Value
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Returns the name of a given case.
     *  eg: StatusCodeCategories::ServerError->getName() // ServerError
     *
     * @return string Case name
     */
    public function getName(): string
    {
        return $this->name;
    }
}
