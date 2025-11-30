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
use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\String_;

/**
 * Value Object representing the type `interface-string`.
 *
 * @psalm-immutable
 */
final class InterfaceString extends String_ implements PseudoType
{
    /** @var Fqsen[] */
    private $fqsens;

    public function __construct(Fqsen ...$fqsens)
    {
        $this->fqsens = $fqsens;
    }

    public function underlyingType(): Type
    {
        return new String_();
    }

    /**
     * @return Fqsen[]
     */
    public function getFqsens(): array
    {
        return $this->fqsens;
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString(): string
    {
        if (!$this->fqsens) {
            return 'interface-string';
        }

        return 'interface-string<' . implode('|', $this->fqsens) . '>';
    }
}
