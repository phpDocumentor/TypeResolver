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
 * Value Object representing the type `trait-string`.
 *
 * @psalm-immutable
 */
final class TraitString extends String_ implements PseudoType
{
    /** @var Type|null */
    private $genericType;

    public function __construct(?Type $genericType = null)
    {
        $this->genericType = $genericType;
    }

    public function underlyingType(): Type
    {
        return new String_();
    }

    public function getGenericType(): ?Type
    {
        return $this->genericType;
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString(): string
    {
        if ($this->genericType === null) {
            return 'trait-string';
        }

        return 'trait-string<' . (string) $this->genericType . '>';
    }
}
