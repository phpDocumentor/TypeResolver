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

use phpDocumentor\Reflection\Types\Float_;
use PHPUnit\Framework\TestCase;

class FloatValueTest extends TestCase
{
    public function testCreate(): void
    {
        $value = 12.12;
        $type = new FloatValue($value);

        $this->assertSame($value, $type->getValue());
        $this->assertEquals(new Float_(), $type->underlyingType());
    }

    public function testToString(): void
    {
        $this->assertSame('12.12', (string) (new FloatValue(12.12)));
    }
}
