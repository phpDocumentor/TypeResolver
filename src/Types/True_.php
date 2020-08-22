<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @link      http://phpdoc.org
 */

declare(strict_types=1);

namespace phpDocumentor\Reflection\Types;

/**
 * Value Object representing a True pseudo type.
 *
 * @psalm-immutable
 */
class True_ extends Boolean
{
    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString() : string
    {
        return 'true';
    }
}
