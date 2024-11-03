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
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Represents a conditional. This is an abstract class for Conditional and ConditionalParameter.
 *
 * @psalm-immutable
 */
abstract class AbstractConditional extends Mixed_ implements PseudoType
{
    /** @var Type */
    protected $target;

    /** @var Type */
    protected $if;

    /** @var Type */
    protected $else;

    /** @var bool */
    protected $negated;

    public function __construct(Type $target, Type $if, Type $else, bool $negated)
    {
        $this->target  = $target;
        $this->if      = $if;
        $this->else    = $else;
        $this->negated = $negated;
    }

    public function getTarget(): Type
    {
        return $this->target;
    }

    public function getIf(): Type
    {
        return $this->if;
    }

    public function getElse(): Type
    {
        return $this->else;
    }

    public function getNegated(): bool
    {
        return $this->negated;
    }

    public function underlyingType(): Type
    {
        return new Mixed_();
    }
}
