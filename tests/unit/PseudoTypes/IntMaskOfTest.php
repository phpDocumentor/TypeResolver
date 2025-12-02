<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use PHPUnit\Framework\TestCase;

final class IntMaskOfTest extends TestCase
{
    public function testCreate(): void
    {
        $childType = new Compound([new IntegerValue(1), new IntegerValue(5), new IntegerValue(10)]);
        $type = new IntMaskOf($childType);

        $this->assertSame($childType, $type->getType());
        $this->assertEquals(new Integer(), $type->underlyingType());
    }

    public function testToString(): void
    {
        $type = new IntMaskOf(new Compound([new IntegerValue(1), new IntegerValue(5), new IntegerValue(10)]));

        $this->assertSame('int-mask-of<1|5|10>', (string) $type);
    }
}
