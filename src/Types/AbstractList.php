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

use function count;
use function implode;

/**
 * Represents a list of values. This is an abstract class for Array_ and Collection.
 *
 * @psalm-immutable
 */
abstract class AbstractList implements Type
{
    /** @var Type */
    protected $valueType;

    /** @var Type|null */
    protected $keyType;

    /** @var Type */
    protected $defaultKeyType;

    /**
     * Initializes this representation of an array with the given Type.
     */
    public function __construct(?Type $valueType = null, ?Type $keyType = null)
    {
        if ($valueType === null) {
            $valueType = new Mixed_();
        }

        $this->valueType      = $valueType;
        $this->defaultKeyType = new Compound([new String_(), new Integer()]);
        $this->keyType        = $keyType;
    }

    /**
     * Returns the type for the keys of this array.
     */
    public function getKeyType(): Type
    {
        return $this->keyType ?? $this->defaultKeyType;
    }

    /**
     * Returns the value for the keys of this array.
     */
    public function getValueType(): Type
    {
        return $this->valueType;
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString(): string
    {
        if ($this->keyType) {
            $classStringValues = [];
            if ($this->valueType instanceof AggregatedType) {
                /**
                 * @psalm-suppress ImpureMethodCall
                 */
                foreach ($this->valueType->getIterator() as $typeInner) {
                    if (!($typeInner instanceof ClassString)) {
                        $classStringValues = [];
                        break;
                    }

                    $fqsenTmp = $typeInner->getFqsen();
                    if (!$fqsenTmp) {
                        continue;
                    }

                    $classStringValues[] = $fqsenTmp->__toString();
                }
            }

            if (count($classStringValues) > 0) {
                return 'array<' . $this->keyType . ',class-string<' . implode('|', $classStringValues) . '>>';
            }

            return 'array<' . $this->keyType . ',' . $this->valueType . '>';
        }

        if ($this->valueType instanceof Mixed_) {
            return 'array';
        }

        if ($this->valueType instanceof Compound) {
            return '(' . $this->valueType . ')[]';
        }

        return $this->valueType . '[]';
    }
}
