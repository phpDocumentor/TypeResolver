<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Types;

use function preg_match;
use function substr;

/**
 * Represents an array type as described in the PSR-5, the PHPDoc Standard.
 *
 * An array can be represented in two forms:
 *
 * 1. Untyped (`array`), where the key and value type is unknown and hence classified as 'Mixed_'.
 * 2. Types (`string[]`), where the value type is provided by preceding an opening and closing square bracket with a
 *    type name.
 *
 * @psalm-immutable
 */
class Array_ extends AbstractList
{
    public function __toString(): string
    {
        if ($this->valueType === null) {
            return 'array';
        }

        $valueTypeString = (string) $this->valueType;

        if ($this->keyType) {
            return 'array<' . $this->keyType . ', ' . $valueTypeString . '>';
        }

        if (!preg_match('/[^\w\\\\]/', $valueTypeString) || substr($valueTypeString, -2, 2) === '[]') {
            return $valueTypeString . '[]';
        }

        return 'array<' . $valueTypeString . '>';
    }
}
