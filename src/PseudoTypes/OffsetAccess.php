<?php
/*
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @link      http://phpdoc.org
 *
 */

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Nullable;

/**
 * Value Object representing the offset access type.
 *
 * @psalm-immutable
 */
final class OffsetAccess extends Mixed_ implements PseudoType
{
    /** @var Type */
    private $type;
    /** @var Type */
    private $offset;

    public function __construct(Type $type, Type $offset)
    {
        $this->type = $type;
        $this->offset = $offset;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getOffset(): Type
    {
        return $this->offset;
    }

    public function underlyingType(): Type
    {
        return new Mixed_();
    }

    public function __toString(): string
    {
        if (
            $this->type instanceof Callable_
            || $this->type instanceof ConstExpression
            || $this->type instanceof Nullable
        ) {
            return '(' . $this->type . ')[' . $this->offset . ']';
        }

        return $this->type . '[' . $this->offset . ']';
    }
}
