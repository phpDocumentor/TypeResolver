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

use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

final class ScalarTest extends TestCase
{
    public function testCreate(): void
    {
        $type = new Scalar();

        $expectedUnderlyingType = new Compound([new String_(), new Integer(), new Float_(), new Boolean()]);
        $this->assertEquals($expectedUnderlyingType, $type->underlyingType());
    }

    public function testToString(): void
    {
        $type = new Scalar();

        $this->assertSame('scalar', (string) $type);
    }
}
