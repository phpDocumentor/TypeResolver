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
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Types\Array_;

/** @psalm-immutable */
class PropertiesOf extends Array_ implements PseudoType
{
    /** @var Fqsen|null */
    protected $fqsen;

    /**
     * Initializes this representation of a class string with the given Fqsen.
     */
    public function __construct(?Fqsen $fqsen = null)
    {
        $this->fqsen = $fqsen;
    }

    /**
     * Returns the FQSEN associated with this object.
     */
    public function getFqsen(): ?Fqsen
    {
        return $this->fqsen;
    }

    public function underlyingType(): Type
    {
        return new Array_();
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString(): string
    {
        return 'properties-of<' . (string) $this->fqsen . '>';
    }
}
