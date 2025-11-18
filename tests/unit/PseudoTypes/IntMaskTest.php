<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\IntMask
 */
class IntMaskTest extends TestCase
{
    /**
     * @covers ::getTypes
     */
    public function testCreate(): void
    {
        $childTypes = [new IntegerValue(1), new IntegerValue(5), new IntegerValue(10)];
        $type = new IntMask(...$childTypes);

        $this->assertSame($childTypes, $type->getTypes());
    }

    /**
     * @covers ::__toString
     */
    public function testToString(): void
    {
        $type = new IntMask(new IntegerValue(1), new IntegerValue(510), new IntegerValue(6000));
        $this->assertSame('int-mask<1, 510, 6000>', (string) $type);
    }
}
