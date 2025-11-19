<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Static_;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\Conditional
 */
class ConditionalTest extends TestCase
{
    /**
     * @covers ::isNegated
     * @covers ::getSubjectType
     * @covers ::getTargetType
     * @covers ::getIf
     * @covers ::getElse
     */
    public function testCreate(): void
    {
        $subjectType = new Object_(new Fqsen('\\phpDocumentor\\T'));
        $targetType = new Integer();
        $if = new Static_();
        $else = new Array_(new Static_());
        $type = new Conditional(false, $subjectType, $targetType, $if, $else);

        $this->assertFalse($type->isNegated());
        $this->assertSame($subjectType, $type->getSubjectType());
        $this->assertSame($targetType, $type->getTargetType());
        $this->assertSame($if, $type->getIf());
        $this->assertSame($else, $type->getElse());
    }

    /**
     * @dataProvider provideToStringData
     * @covers ::__toString
     */
    public function testToString(string $expectedResult, Conditional $type): void
    {
        $this->assertSame($expectedResult, (string) $type);
    }

    /**
     * @return array<string, array{string, Conditional}>
     */
    public static function provideToStringData(): array
    {
        return [
            'basic' => [
                '(\\phpDocumentor\\T is int ? static : static[])',
                new Conditional(
                    false,
                    new Object_(new Fqsen('\\phpDocumentor\\T')),
                    new Integer(),
                    new Static_(),
                    new Array_(new Static_())
                ),
            ],
            'negated' => [
                '(\\phpDocumentor\\T is not int ? static : static[])',
                new Conditional(
                    true,
                    new Object_(new Fqsen('\\phpDocumentor\\T')),
                    new Integer(),
                    new Static_(),
                    new Array_(new Static_())
                ),
            ],
        ];
    }
}
