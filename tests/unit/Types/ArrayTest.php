<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Array_
 */
class ArrayTest extends TestCase
{
    /**
     * @covers ::getOriginalKeyType
     * @covers ::getOriginalValueType
     * @covers ::getKeyType
     * @covers ::getValueType
     */
    public function testCreateWithoutParams(): void
    {
        $type = new Array_();

        $this->assertNull($type->getOriginalKeyType());
        $this->assertNull($type->getOriginalValueType());
        $this->assertEquals(new Compound([new String_(), new Integer()]), $type->getKeyType());
        $this->assertEquals(new Mixed_(), $type->getValueType());
    }

    /**
     * @covers ::getOriginalKeyType
     * @covers ::getOriginalValueType
     * @covers ::getKeyType
     * @covers ::getValueType
     */
    public function testCreateWithParams(): void
    {
        $valueType = new Object_(new Fqsen('\\phpDocumentor\\Foo\\Bar'));
        $keyType = new Compound(
            [
                new String_(),
                new Integer(),
            ]
        );

        $type = new Array_($valueType, $keyType);

        $this->assertSame($keyType, $type->getOriginalKeyType());
        $this->assertSame($valueType, $type->getOriginalValueType());
        $this->assertSame($keyType, $type->getKeyType());
        $this->assertSame($valueType, $type->getValueType());
    }

    /**
     * @dataProvider provideArrays
     * @covers ::__toString
     */
    public function testArrayStringifyCorrectly(Array_ $array, string $expectedString): void
    {
        $this->assertSame($expectedString, (string) $array);
    }

    /**
     * @return mixed[]
     */
    public function provideArrays(): array
    {
        return [
            'simple array' => [new Array_(), 'array'],
            'array of mixed' => [new Array_(new Mixed_()), 'mixed[]'],
            'array of single type' => [new Array_(new String_()), 'string[]'],
            'array of compound type' => [new Array_(new Compound([new Integer(), new String_()])), '(int|string)[]'],
            'array with key type' => [new Array_(new String_(), new Integer()), 'array<int,string>'],
        ];
    }
}
