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
use phpDocumentor\Reflection\Types\Resource_;

/**
 * Value Object representing the type 'open-resource'.
 *
 * @psalm-immutable
 */
final class OpenResource extends Resource_ implements PseudoType
{
    public function underlyingType(): Type
    {
        return new Resource_();
    }

    public function __toString(): string
    {
        return 'open-resource';
    }
}
