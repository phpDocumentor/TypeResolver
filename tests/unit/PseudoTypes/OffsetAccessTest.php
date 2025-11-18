<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\OffsetAccess
 */
class OffsetAccessTest extends TestCase
{
    /**
     * @covers ::getType
     * @covers ::getOffset
     */
    public function testCreate(): void
    {
        $mainType = new Object_(new Fqsen('\\phpDocumentor\\MyArray'));
        $offset = new StringValue('bar');
        $type = new OffsetAccess($mainType, $offset);

        $this->assertSame($mainType, $type->getType());
        $this->assertSame($offset, $type->getOffset());
    }

    /**
     * @covers ::__toString
     */
    public function testToString(): void
    {
        $type = new OffsetAccess(new Object_(new Fqsen('\\phpDocumentor\\MyArray')), new StringValue('bar'));

        $this->assertSame('\\phpDocumentor\\MyArray["bar"]', (string) $type);
    }
}
