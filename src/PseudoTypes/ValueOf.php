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
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\PseudoType;

/** @psalm-immutable */
final class ValueOf extends Mixed_ implements PseudoType
{
    /** @var Type */
    protected $valueType;

    public function __construct(Type $valueType)
    {
        $this->valueType = $valueType;
    }

    public function getValueType(): Type
    {
        return $this->valueType;
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
        return 'value-of<' . $this->valueType . '>';
    }
}
