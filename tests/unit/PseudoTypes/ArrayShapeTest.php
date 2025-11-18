<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\ArrayShape
 */
class ArrayShapeTest extends TestCase
{
    /**
     * @covers ::getItems
     */
    public function testExposeItems(): void
    {
        $item1 = new ArrayShapeItem('foo', new True_(), false);
        $item2 = new ArrayShapeItem('bar', new False_(), true);

        $arrayShape = new ArrayShape($item1, $item2);

        $this->assertSame([$item1, $item2], $arrayShape->getItems());
    }

    /**
     * @dataProvider provideToStringData
     * @covers ::__toString
     */
    public function testToString(string $expectedResult, ArrayShape $arrayShape): void
    {
        $this->assertSame($expectedResult, (string) $arrayShape);
    }

    /**
     * @return array<string, array{string, ArrayShape}>
     */
    public static function provideToStringData(): array
    {
        return [
            'with keys' => [
                'array{foo: true, bar?: false}',
                new ArrayShape(
                    new ArrayShapeItem('foo', new True_(), false),
                    new ArrayShapeItem('bar', new False_(), true)
                ),
            ],
            'with empty keys' => [
                'array{true, false}',
                new ArrayShape(
                    new ArrayShapeItem('', new True_(), false),
                    new ArrayShapeItem('', new False_(), false)
                ),
            ],
            'without keys' => [
                'array{true, false}',
                new ArrayShape(
                    new ArrayShapeItem(null, new True_(), false),
                    new ArrayShapeItem(null, new False_(), false)
                ),
            ],
        ];
    }
}
