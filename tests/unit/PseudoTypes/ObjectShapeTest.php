<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;

final class ObjectShapeTest extends TestCase
{
    public function testCreate(): void
    {
        $item1 = new ObjectShapeItem('foo', new True_(), false);
        $item2 = new ObjectShapeItem('bar', new False_(), true);

        $objectShape = new ObjectShape($item1, $item2);

        $this->assertSame([$item1, $item2], $objectShape->getItems());
        $this->assertEquals(new Object_(), $objectShape->underlyingType());
    }

    /**
     * @dataProvider provideToStringData
     */
    public function testToString(string $expectedResult, ObjectShape $objectShape): void
    {
        $this->assertSame($expectedResult, (string) $objectShape);
    }

    /**
     * @return array<string, array{string, ObjectShape}>
     */
    public static function provideToStringData(): array
    {
        return [
            'with keys' => [
                'object{foo: true, bar?: false}',
                new ObjectShape(
                    new ObjectShapeItem('foo', new True_(), false),
                    new ObjectShapeItem('bar', new False_(), true)
                ),
            ],
            'with empty keys' => [
                'object{true, false}',
                new ObjectShape(
                    new ObjectShapeItem('', new True_(), false),
                    new ObjectShapeItem('', new False_(), false)
                ),
            ],
            'without keys' => [
                'object{true, false}',
                new ObjectShape(
                    new ObjectShapeItem(null, new True_(), false),
                    new ObjectShapeItem(null, new False_(), false)
                ),
            ],
        ];
    }
}
