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

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Object_;

/**
 * Value Object representing the wrapper over the generic.
 *
 * @psalm-immutable
 */
final class GenericTemplate implements Type
{
    /** @var Object_ */
    private $resolvedType;

    public function __construct(Object_ $resolvedType)
    {
        $this->resolvedType = $resolvedType;
    }

    public function getResolvedType(): Object_
    {
        return $this->resolvedType;
    }

    public function __toString(): string
    {
        return (string) $this->resolvedType;
    }
}
