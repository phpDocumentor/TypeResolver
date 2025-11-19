<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Static_
 */
class StaticTest extends TestCase
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

        $type = new Static_(...$genericTypes);

        $this->assertSame($genericTypes, $type->getGenericTypes());
    }

    /**
     * @dataProvider provideToStringData
     * @covers ::__toString
     */
    public function testToString(string $expectedResult, Static_ $type): void
    {
        $this->assertSame($expectedResult, (string) $type);
    }

    /**
     * @return array<string, array{string, Static_}>
     */
    public static function provideToStringData(): array
    {
        return [
            'basic' => [
                'static',
                new Static_(),
            ],
            'with generic' => [
                'static<\\phpDocumentor\\FirstClass, \\phpDocumentor\\SecondClass, \\phpDocumentor\\ThirdClass>',
                new Static_(
                    new Object_(new Fqsen('\\phpDocumentor\\FirstClass')),
                    new Object_(new Fqsen('\\phpDocumentor\\SecondClass')),
                    new Object_(new Fqsen('\\phpDocumentor\\ThirdClass')),
                ),
            ],
        ];
    }
}
