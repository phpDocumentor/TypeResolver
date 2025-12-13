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
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Value Object representing the type 'callable-array'.
 *
 * @psalm-immutable
 */
final class CallableArray extends Array_ implements PseudoType
{
    public function __construct()
    {
        parent::__construct(new Mixed_(), new Integer());
    }

    public function underlyingType(): Type
    {
        return new Array_(new Mixed_(), new Integer());
    }

    public function __toString(): string
    {
        return 'callable-array';
    }
}
