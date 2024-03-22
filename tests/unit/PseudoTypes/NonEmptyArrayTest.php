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

use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\NonEmptyArray
 */
class NonEmptyArrayTest extends TestCase
{
    /**
     * @dataProvider provideArrays
     * @covers ::__toString
     */
    public function testArrayStringifyCorrectly(NonEmptyArray $array, string $expectedString): void
    {
        $this->assertSame($expectedString, (string) $array);
    }

    /**
     * @return mixed[]
     */
    public function provideArrays(): array
    {
        return [
            'simple non-empty-array' => [new NonEmptyArray(), 'non-empty-array'],
            'non-empty-array of mixed' => [new NonEmptyArray(new Mixed_()), 'non-empty-array'],
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
