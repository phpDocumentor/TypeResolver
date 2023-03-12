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
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\NonEmptyList
 */
class NonEmptyListTest extends TestCase
{
    /**
     * @dataProvider provideArrays
     * @covers ::__toString
     */
    public function testArrayStringifyCorrectly(NonEmptyList $array, string $expectedString): void
    {
        $this->assertSame($expectedString, (string) $array);
    }

    /**
     * @return mixed[]
     */
    public function provideArrays(): array
    {
        return [
            'simple non-empty-list' => [new NonEmptyList(), 'non-empty-list'],
            'non-empty-list of mixed' => [new NonEmptyList(new Mixed_()), 'non-empty-list'],
            'non-empty-list of single type' => [new NonEmptyList(new String_()), 'non-empty-list<string>'],
            'non-empty-list of compound type' =>
                [new NonEmptyList(new Compound([new Integer(), new String_()])), 'non-empty-list<int|string>'],
        ];
    }
}
