<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Self_
 */
class SelfTest extends TestCase
{
    /**
     * @covers ::getGenericTypes
     */
    public function testCreate(): void
    {
        $genericTypes = [
            new Object_(new Fqsen('\\phpDocumentor\\FirstClass')),
            new Object_(new Fqsen('\\phpDocumentor\\SecondClass')),
            new Object_(new Fqsen('\\phpDocumentor\\ThirdClass')),
        ];

        $type = new Self_(...$genericTypes);

        $this->assertSame($genericTypes, $type->getGenericTypes());
    }

    /**
     * @dataProvider provideToStringData
     * @covers ::__toString
     */
    public function testToString(string $expectedResult, Self_ $type): void
    {
        $this->assertSame($expectedResult, (string) $type);
    }

    /**
     * @return array<string, array{string, Self_}>
     */
    public static function provideToStringData(): array
    {
        return [
            'basic' => [
                'self',
                new Self_(),
            ],
            'with generic' => [
                'self<\\phpDocumentor\\FirstClass, \\phpDocumentor\\SecondClass, \\phpDocumentor\\ThirdClass>',
                new Self_(
                    new Object_(new Fqsen('\\phpDocumentor\\FirstClass')),
                    new Object_(new Fqsen('\\phpDocumentor\\SecondClass')),
                    new Object_(new Fqsen('\\phpDocumentor\\ThirdClass')),
                ),
            ],
        ];
    }
}
