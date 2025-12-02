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

use InvalidArgumentException;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

final class ObjectTest extends TestCase
{
    public function testCreateWithInvalidFqsen(): void
    {
        $fqsen = new Fqsen('\\Foo::BAR');

        $this->expectExceptionObject(
            new InvalidArgumentException(
                'Object types can only refer to a class, interface or trait but a method, function, constant or '
                . 'property was received: ' . (string) $fqsen
            )
        );

        new Object_($fqsen);
    }
}
