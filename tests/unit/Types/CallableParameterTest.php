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

use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

final class CallableParameterTest extends TestCase
{
    public function testCreate(): void
    {
        $type = new Object_(new Fqsen('\\phpDocumentor\\A'));
        $name = 'param';
        $parameter = new CallableParameter(
            $type,
            $name,
            true,
            true,
            true
        );

        $this->assertSame($type, $parameter->getType());
        $this->assertSame($name, $parameter->getName());
        $this->assertTrue($parameter->isOptional());
        $this->assertTrue($parameter->isReference());
        $this->assertTrue($parameter->isVariadic());
    }
}
