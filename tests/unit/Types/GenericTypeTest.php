<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\PseudoTypes\List_;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Types\GenericType
 */
class GenericTypeTest extends TestCase
{
    /**
     * @covers ::getFqsen
     * @covers ::getTypes
     */
    public function testCreate(): void
    {
        $fqsen = new Fqsen('\\Foo\\Bar');
        $types = [new Object_(new Fqsen('\\Foo\\SomeClass')), new List_(new Integer())];
        $type = new GenericType($fqsen, $types);

        $this->assertSame($fqsen, $type->getFqsen());
        $this->assertSame($types, $type->getTypes());
    }

    /**
     * @dataProvider provideToStringData
     * @covers ::__toString
     */
    public function testToString(string $expectedResult, GenericType $type): void
    {
        $this->assertSame($expectedResult, (string) $type);
    }

    /**
     * @return array<string, array{string, GenericType}>
     */
    public static function provideToStringData(): array
    {
        return [
            'without fqsen' => [
                'object<string>',
                new GenericType(
                    null,
                    [
                        new String_(),
                    ]
                ),
            ],
            'collection without key' => [
                '\\ArrayObject<string>',
                new GenericType(new Fqsen('\\ArrayObject'), [new String_()]),
            ],
            'collection with key' => [
                '\\ArrayObject<string[], \\Iterator>',
                new GenericType(
                    new Fqsen('\\ArrayObject'),
                    [new Array_(new String_()), new Object_(new Fqsen('\\Iterator'))]
                ),
            ],
            'more than two generics' => [
                '\\MyClass<\\SomeClassFirst, \\SomeClassSecond, \\SomeClassThird>',
                new GenericType(
                    new Fqsen('\\MyClass'),
                    [
                        new Object_(new Fqsen('\\SomeClassFirst')),
                        new Object_(new Fqsen('\\SomeClassSecond')),
                        new Object_(new Fqsen('\\SomeClassThird')),
                    ]
                ),
            ],
        ];
    }
}
