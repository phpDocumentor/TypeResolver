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

use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;
use PHPUnit\Framework\TestCase;

final class CallableArrayTest extends TestCase
{
    public function testCreate(): void
    {
        $type = new CallableArray();

        $this->assertEquals(new Array_(new Mixed_(), new Integer()), $type->underlyingType());
        $this->assertEquals(new Mixed_(), $type->getValueType());
        $this->assertEquals(new Integer(), $type->getKeyType());
    }

    public function testToString(): void
    {
        $this->assertSame('callable-array', (string) (new CallableArray()));
    }
}
