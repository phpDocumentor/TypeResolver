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
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Nullable;

/** @psalm-immutable */
final class OffsetAccess extends Mixed_ implements PseudoType
{
    /** @var Type */
    private $valueType;

    /** @var Type */
    private $offsetType;

    public function __construct(Type $valueType, Type $offsetType)
    {
        $this->valueType  = $valueType;
        $this->offsetType = $offsetType;
    }

    public function getValueType(): Type
    {
        return $this->valueType;
    }

    public function getOffsetType(): Type
    {
        return $this->offsetType;
    }

    public function underlyingType(): Type
    {
        return new Mixed_();
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString(): string
    {
        if (
            $this->valueType instanceof Callable_
            || $this->valueType instanceof Nullable
        ) {
            return '(' . $this->valueType . ')[' . $this->offsetType . ']';
        }

        return $this->valueType . '[' . $this->offsetType . ']';
    }
}
