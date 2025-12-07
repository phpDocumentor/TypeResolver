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
 * Value Object representing the `protected-properties-of` type.
 *
 * @psalm-immutable
 */
final class ProtectedPropertiesOf extends PropertiesOf
{
    public function __toString(): string
    {
        return 'protected-properties-of<' . $this->type . '>';
    }
}
