<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;

final class ValueOfTest extends TestCase
{
    public function testCreate(): void
    {
        $childType = new ConstExpression(new Object_(new Fqsen('\\phpDocumentor\\Type')), 'ARRAY_CONST');
        $type = new ValueOf($childType);

        $this->assertSame($childType, $type->getType());
        $this->assertEquals(new Mixed_(), $type->underlyingType());
    }

    public function testToString(): void
    {
        $type = new ValueOf(new ConstExpression(new Object_(new Fqsen('\\phpDocumentor\\Type')), 'ARRAY_CONST'));

        $this->assertSame('value-of<\\phpDocumentor\\Type::ARRAY_CONST>', (string) $type);
    }
}
