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
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\String_;

/**
 * Value Object representing the `properties-of` type.
 *
 * @psalm-immutable
 */
class PropertiesOf extends Array_ implements PseudoType
{
    /** @var Type */
    protected $type;

    public function __construct(Type $type)
    {
        parent::__construct(new Mixed_(), new String_());

        $this->type = $type;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function underlyingType(): Type
    {
        return new Array_(new Mixed_(), new String_());
    }

    public function __toString(): string
    {
        return 'properties-of<' . $this->type . '>';
    }
}
