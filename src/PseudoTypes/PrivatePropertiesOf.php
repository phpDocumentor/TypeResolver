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

/**
 * Value Object representing the `private-properties-of` type.
 *
 * @psalm-immutable
 */
final class PrivatePropertiesOf extends PropertiesOf
{
    public function __toString(): string
    {
        return 'private-properties-of<' . $this->type . '>';
    }
}
