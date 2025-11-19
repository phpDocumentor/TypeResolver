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
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Value Object representing the `value-of` type.
 *
 * @psalm-immutable
 */
final class ValueOf extends Mixed_ implements PseudoType
{
    /** @var Type */
    private $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function underlyingType(): Type
    {
        return new Mixed_();
    }

    public function __toString(): string
    {
        return 'value-of<' . $this->type . '>';
    }
}
