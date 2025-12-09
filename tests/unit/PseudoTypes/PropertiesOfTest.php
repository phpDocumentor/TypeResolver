<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

final class PropertiesOfTest extends TestCase
{
    public function testCreate(): void
    {
        $childType = new Self_();
        $type = new PropertiesOf($childType);

        $this->assertSame($childType, $type->getType());
        $this->assertEquals(new Array_(new Mixed_(), new String_()), $type->underlyingType());
        $this->assertEquals(new String_(), $type->getKeyType());
        $this->assertEquals(new Mixed_(), $type->getValueType());
    }

    public function testToString(): void
    {
        $type = new PropertiesOf(new Self_());

        $this->assertSame('properties-of<self>', (string) $type);
    }
}
