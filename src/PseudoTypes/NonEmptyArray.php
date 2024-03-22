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
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Value Object representing the type 'non-empty-array'.
 *
 * @psalm-immutable
 */
final class NonEmptyArray extends Array_ implements PseudoType
{
    public function underlyingType(): Type
    {
        return new Array_($this->valueType, $this->keyType);
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString(): string
    {
        if ($this->keyType) {
            return 'non-empty-array<' . $this->keyType . ',' . $this->valueType . '>';
        }

        if ($this->valueType instanceof Mixed_) {
            return 'non-empty-array';
        }

        return 'non-empty-array<' . $this->valueType . '>';
    }
}
