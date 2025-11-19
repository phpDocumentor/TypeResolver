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
     * @dataProvider provideToStringData
     * @covers ::__toString
     */
    public function testToString(string $expectedResult, OffsetAccess $type): void
    {
        $this->assertSame($expectedResult, (string) $type);
    }

    /**
     * @return array<string, array{string, OffsetAccess}>
     */
    public static function provideToStringData(): array
    {
        return [
            'basic' => [
                '\\phpDocumentor\\MyArray["bar"]',
                new OffsetAccess(new Object_(new Fqsen('\\phpDocumentor\\MyArray')), new StringValue('bar')),
            ],
            'with const expression' => [
                '(\\phpDocumentor\\Foo::SOME_ARRAY)["bar"]',
                new OffsetAccess(
                    new ConstExpression(new Object_(new Fqsen('\\phpDocumentor\\Foo')), 'SOME_ARRAY'),
                    new StringValue('bar')
                ),
            ],
        ];
    }
}
