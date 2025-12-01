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

use phpDocumentor\Reflection\Types\Integer;
use PHPUnit\Framework\TestCase;

class IntegerRangeTest extends TestCase
{
    public function testCreate(): void
    {
        $minValue = '-5';
        $maxValue = '5';
        $type = new IntegerRange($minValue, $maxValue);

        $this->assertSame($minValue, $type->getMinValue());
        $this->assertSame($maxValue, $type->getMaxValue());
        $this->assertEquals(new Integer(), $type->underlyingType());
    }

    /**
     * @dataProvider provideToStringData
     */
    public function testToString(IntegerRange $type, string $expectedString): void
    {
        $this->assertSame($expectedString, (string) $type);
    }

    /**
     * @return array<string, array{IntegerRange, string}>
     */
    public function provideToStringData(): array
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
