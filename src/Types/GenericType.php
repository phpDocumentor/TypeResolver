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

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;

use function implode;

/**
 * Value Object representing a type with generics.
 *
 * @psalm-immutable
 */
final class GenericType implements Type
{
    /** @var Fqsen|null */
    private $fqsen;

    /** @var Type[] */
    private $types;

    /**
     * @param Type[] $types
     */
    public function __construct(?Fqsen $fqsen, array $types)
    {
        $this->fqsen = $fqsen;
        $this->types = $types;
    }

    public function getFqsen(): ?Fqsen
    {
        return $this->fqsen;
    }

    /**
     * @return Type[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function __toString(): string
    {
        $objectType = (string) ($this->fqsen ?? 'object');

        return $objectType . '<' . implode(', ', $this->types) . '>';
    }
}
