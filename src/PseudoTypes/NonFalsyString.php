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

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\String_;

/**
 * Value Object representing the type 'non-falsy-string'.
 *
 * @psalm-immutable
 */
final class NonFalsyString extends String_ implements PseudoType
{
    public function underlyingType(): Type
    {
        return new String_();
    }

    public function __toString(): string
    {
        return 'non-falsy-string';
    }
}
