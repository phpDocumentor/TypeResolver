<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\KeyOf
 */
class KeyOfTest extends TestCase
{
    /**
     * @covers ::getType
     */
    public function testCreate(): void
    {
        $childType = new ConstExpression(new Object_(new Fqsen('\\phpDocumentor\\Type')), 'ARRAY_CONST');
        $type = new KeyOf($childType);

        $this->assertSame($childType, $type->getType());
    }

    /**
     * @covers ::__toString
     */
    public function testToString(): void
    {
        $type = new KeyOf(new ConstExpression(new Object_(new Fqsen('\\phpDocumentor\\Type')), 'ARRAY_CONST'));

        $this->assertSame('key-of<\\phpDocumentor\\Type::ARRAY_CONST>', (string) $type);
    }
}
