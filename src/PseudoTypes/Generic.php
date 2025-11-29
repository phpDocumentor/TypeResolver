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

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Object_;

use function implode;

/**
 * Value Object representing a type with generics.
 *
 * @psalm-immutable
 */
final class Generic extends Object_
{
    /** @var Type[] */
    private $types;

    /**
     * @param Type[] $types
     */
    public function __construct(?Fqsen $fqsen, array $types)
    {
        parent::__construct($fqsen);

        $this->types = $types;
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
