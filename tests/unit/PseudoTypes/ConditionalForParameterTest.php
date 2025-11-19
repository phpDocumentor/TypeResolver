<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Static_;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\ConditionalForParameter
 */
class ConditionalForParameterTest extends TestCase
{
    /**
     * @covers ::isNegated
     * @covers ::getParameterName
     * @covers ::getTargetType
     * @covers ::getIf
     * @covers ::getElse
     */
    public function testCreate(): void
    {
        $parameterName = 'some';
        $targetType = new Integer();
        $if = new Static_();
        $else = new Array_(new Static_());
        $type = new ConditionalForParameter(false, $parameterName, $targetType, $if, $else);

        $this->assertFalse($type->isNegated());
        $this->assertSame($parameterName, $type->getParameterName());
        $this->assertSame($targetType, $type->getTargetType());
        $this->assertSame($if, $type->getIf());
        $this->assertSame($else, $type->getElse());
    }

    /**
     * @dataProvider provideToStringData
     * @covers ::__toString
     */
    public function testToString(string $expectedResult, ConditionalForParameter $type): void
    {
        $this->assertSame($expectedResult, (string) $type);
    }

    /**
     * @return array<string, array{string, ConditionalForParameter}>
     */
    public static function provideToStringData(): array
    {
        return [
            'basic' => [
                '($test is int ? static : static[])',
                new ConditionalForParameter(
                    false,
                    'test',
                    new Integer(),
                    new Static_(),
                    new Array_(new Static_())
                ),
            ],
            'negated' => [
                '($test2 is not int ? static : static[])',
                new ConditionalForParameter(
                    true,
                    'test2',
                    new Integer(),
                    new Static_(),
                    new Array_(new Static_())
                ),
            ],
        ];
    }
}
