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
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Iterable_
 */
class IterableTest extends TestCase
{
    /**
     * @covers ::__toString
     * @dataProvider provideIterables
     */
    public function testIterableStringifyCorrectly(Iterable_ $iterable, string $expectedString) : void
    {
        $this->assertSame($expectedString, (string) $iterable);
    }

    /**
     * @return mixed[]
     */
    public function provideIterables() : array
    {
        return [
            'simple iterable' => [new Iterable_(), 'iterable'],
            'iterable of mixed' => [new Iterable_(new Mixed_()), 'iterable'],
            'iterable of single type' => [new Iterable_(new String_()), 'iterable<string>'],
            'iterable of compound type' => [
                new Iterable_(new Compound([new Integer(), new String_()])),
                'iterable<int|string>',
            ],
            'iterable with key type' => [new Iterable_(new String_(), new Integer()), 'iterable<int,string>'],
        ];
    }
}
