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

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Array_
 */
class ArrayTest extends TestCase
{
    /**
     * @dataProvider provideArrays
     * @covers ::__toString
     */
    public function testArrayStringifyCorrectly(Array_ $array, string $expectedString) : void
    {
        $this->assertSame($expectedString, (string) $array);
    }

    /**
     * @return mixed[]
     */
    public function provideArrays() : array
    {
        return [
            'simple array' => [new Array_(), 'array'],
            'array of mixed' => [new Array_(new Mixed_()), 'array'],
            'array of single type' => [new Array_(new String_()), 'string[]'],
            'array of compound type' => [new Array_(new Compound([new Integer(), new String_()])), '(int|string)[]'],
            'array with key type' => [new Array_(new String_(), new Integer()), 'array<int,string>'],
        ];
    }
}
