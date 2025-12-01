<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

class GenericTest extends TestCase
{
    public function testCreate(): void
    {
        $fqsen = new Fqsen('\\Foo\\Bar');
        $types = [new Object_(new Fqsen('\\Foo\\SomeClass')), new List_(new Integer())];
        $type = new Generic($fqsen, $types);

        $this->assertSame($fqsen, $type->getFqsen());
        $this->assertSame($types, $type->getTypes());
    }

    /**
     * @dataProvider provideToStringData
     */
    public function testToString(string $expectedResult, Generic $type): void
    {
        $this->assertSame($expectedResult, (string) $type);
    }

    /**
     * @return array<string, array{string, Generic}>
     */
    public static function provideToStringData(): array
    {
        return [
            'without fqsen' => [
                'object<string>',
                new Generic(
                    null,
                    [
                        new String_(),
                    ]
                ),
            ],
            'collection without key' => [
                '\\ArrayObject<string>',
                new Generic(new Fqsen('\\ArrayObject'), [new String_()]),
            ],
            'collection with key' => [
                '\\ArrayObject<string[], \\Iterator>',
                new Generic(
                    new Fqsen('\\ArrayObject'),
                    [new Array_(new String_()), new Object_(new Fqsen('\\Iterator'))]
                ),
            ],
            'more than two generics' => [
                '\\MyClass<\\T, \\SomeClassSecond, \\SomeClassThird>',
                new Generic(
                    new Fqsen('\\MyClass'),
                    [
                        new Object_(new Fqsen('\\T')),
                        new Object_(new Fqsen('\\SomeClassSecond')),
                        new Object_(new Fqsen('\\SomeClassThird')),
                    ]
                ),
            ],
        ];
    }
}
