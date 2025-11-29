<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\GenericTemplate
 */
class GenericTemplateTest extends TestCase
{
    /**
     * @covers ::getResolvedType
     */
    public function testCreate(): void
    {
        $resolvedType = new Object_(new Fqsen('\\Foo\\SomeClass'));
        $type = new GenericTemplate($resolvedType);

        $this->assertSame($resolvedType, $type->getResolvedType());
    }

    /**
     * @covers ::__toString
     */
    public function testToString(): void
    {
        $type = new GenericTemplate(new Object_(new Fqsen('\\Foo\\SomeClass')));
        $this->assertSame('\\Foo\\SomeClass', (string) $type);
    }
}
