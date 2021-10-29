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
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\List_
 */
class ListTest extends TestCase
{
    /**
     * @dataProvider provideArrays
     * @covers ::__toString
     */
    public function testArrayStringifyCorrectly(List_ $array, string $expectedString): void
    {
        $this->assertSame($expectedString, (string) $array);
    }

    /**
     * @return mixed[]
     */
    public function provideArrays(): array
    {
        return [
            'simple list' => [new List_(), 'list'],
            'list of mixed' => [new List_(new Mixed_()), 'list'],
            'list of single type' => [new List_(new String_()), 'list<string>'],
            'list of compound type' => [new List_(new Compound([new Integer(), new String_()])), 'list<int|string>'],
        ];
    }
}
