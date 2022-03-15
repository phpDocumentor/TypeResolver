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

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\List_
 */
class IntRangeTest extends TestCase
{
    /**
     * @dataProvider provideArrays
     * @covers ::__toString
     */
    public function testArrayStringifyCorrectly(IntegerRange $array, string $expectedString): void
    {
        $this->assertSame($expectedString, (string) $array);
    }

    /**
     * @return mixed[]
     */
    public function provideArrays(): array
    {
        return [
            'simple int range' => [new IntegerRange('-5', '5'), 'int<-5, 5>'],
            'zero int range v1' => [new IntegerRange('0', '1'), 'int<0, 1>'],
            'zero int range v2' => [new IntegerRange('-5', '0'), 'int<-5, 0>'],
            'mixed int range' => [new IntegerRange('min', '5'), 'int<min, 5>'],
            'keyword int range' => [new IntegerRange('min', 'max'), 'int<min, max>'],
        ];
    }
}
