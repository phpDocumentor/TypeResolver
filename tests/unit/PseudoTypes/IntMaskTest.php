<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use PHPUnit\Framework\TestCase;

class IntMaskTest extends TestCase
{
    public function testCreate(): void
    {
        $childTypes = [new IntegerValue(1), new IntegerValue(5), new IntegerValue(10)];
        $type = new IntMask(...$childTypes);

        $this->assertSame($childTypes, $type->getTypes());
    }

    public function testToString(): void
    {
        $type = new IntMask(new IntegerValue(1), new IntegerValue(510), new IntegerValue(6000));
        $this->assertSame('int-mask<1, 510, 6000>', (string) $type);
    }
}
