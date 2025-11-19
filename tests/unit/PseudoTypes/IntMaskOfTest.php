<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Types\Compound;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\IntMaskOf
 */
class IntMaskOfTest extends TestCase
{
    /**
     * @covers ::getType
     */
    public function testCreate(): void
    {
        $childType = new Compound([new IntegerValue(1), new IntegerValue(5), new IntegerValue(10)]);
        $type = new IntMaskOf($childType);

        $this->assertSame($childType, $type->getType());
    }

    /**
     * @covers ::__toString
     */
    public function testToString(): void
    {
        $type = new IntMask(new Compound([new IntegerValue(1), new IntegerValue(5), new IntegerValue(10)]));

        $this->assertSame('int-mask<1|5|10>', (string) $type);
    }
}
