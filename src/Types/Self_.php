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

use phpDocumentor\Reflection\Type;

use function implode;

/**
 * Value Object representing the 'self' type.
 *
 * Self, as a Type, represents the class in which the associated element was defined.
 *
 * @psalm-immutable
 */
final class Self_ implements Type
{
    /** @var Type[] */
    private $genericTypes;

    public function __construct(Type ...$genericTypes)
    {
        $this->genericTypes = $genericTypes;
    }

    /**
     * @return Type[]
     */
    public function getGenericTypes(): array
    {
        return $this->genericTypes;
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString(): string
    {
        if ($this->genericTypes) {
            return 'self<' . implode(', ', $this->genericTypes) . '>';
        }

        return 'self';
    }
}
