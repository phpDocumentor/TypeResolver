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

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

class NonEmptyArrayTest extends TestCase
{
    public function testCreateWithoutParams(): void
    {
        $type = new NonEmptyArray();

        $this->assertNull($type->getOriginalKeyType());
        $this->assertNull($type->getOriginalValueType());
        $this->assertEquals(new Compound([new String_(), new Integer()]), $type->getKeyType());
        $this->assertEquals(new Mixed_(), $type->getValueType());
        $this->assertEquals(new Array_(), $type->underlyingType());
    }

    /**
     * @dataProvider provideToStringData
     */
    public function testToString(NonEmptyArray $type, string $expectedString): void
    {
        $this->assertSame($expectedString, (string) $type);
    }

    /**
     * @return array<string, array{NonEmptyArray, string}>
     */
    public function provideToStringData(): array
    {
        return [
            'simple non-empty-array' => [new NonEmptyArray(), 'non-empty-array'],
            'non-empty-array of mixed' => [new NonEmptyArray(new Mixed_()), 'non-empty-array<mixed>'],
            'non-empty-array of single type' => [new NonEmptyArray(new String_()), 'non-empty-array<string>'],
            'non-empty-array of compound type' =>
                [
                    new NonEmptyArray(
                        new Compound([new Integer(), new String_()]),
                        new String_()
                    ),
                    'non-empty-array<string,int|string>',
                ],
        ];
    }
}
