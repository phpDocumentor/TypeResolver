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

/**
 * Represents a list of values. This is an abstract class for Array_ and List_.
 *
 * @psalm-immutable
 */
abstract class AbstractList implements Type
{
    /** @var Type|null */
    protected $valueType;

    /** @var Type|null */
    protected $keyType;

    /** @var Type */
    protected $defaultKeyType;

    /** @var Type */
    protected $defaultValueType;

    /**
     * Initializes this representation of an array with the given Type.
     */
    public function __construct(?Type $valueType = null, ?Type $keyType = null)
    {
        $this->defaultValueType = new Mixed_();
        $this->valueType      = $valueType;
        $this->defaultKeyType = new Compound([new String_(), new Integer()]);
        $this->keyType        = $keyType;
    }

    public function getOriginalKeyType(): ?Type
    {
        return $this->keyType;
    }

    public function getOriginalValueType(): ?Type
    {
        return $this->valueType;
    }

    /**
     * Returns the type for the keys of this array.
     */
    public function getKeyType(): Type
    {
        return $this->keyType ?? $this->defaultKeyType;
    }

    /**
     * Returns the type for the values of this array.
     */
    public function getValueType(): Type
    {
        return $this->valueType ?? $this->defaultValueType;
    }
}
