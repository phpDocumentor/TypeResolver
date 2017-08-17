<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Fqsen;

/**
 * Represents a collection type as described in the PSR-5, the PHPDoc Standard.
 *
 * A collection can be represented in two forms:
 *
 * 1. `ACollectionObject<aValueType>`
 * 2. `ACollectionObject<aValueType,aKeyType>`
 *
 * - ACollectionObject can be 'array' or an object that can act as an array
 * - aValueType and aKeyType can be any type expression
 */
final class Collection extends AbstractList
{

    /** @var Fqsen */
    private $fqsen;

    /**
     * Initializes this representation of an array with the given Type or Fqsen.
     *
     * @param Type $valueType
     * @param Type $keyType
     */
    public function __construct(Fqsen $fqsen, Type $valueType, Type $keyType = null)
    {
        parent::__construct($valueType, $keyType);

        $this->fqsen = $fqsen;
    }

    /**
     * Returns the FQSEN associated with this object.
     *
     * @return Fqsen
     */
    public function getFqsen()
    {
        return $this->fqsen;
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->keyType === null) {
            return $this->fqsen.'<'.$this->valueType . '>';
        }

        return $this->fqsen.'<'.$this->keyType . ',' . $this->valueType . '>';
    }
}
