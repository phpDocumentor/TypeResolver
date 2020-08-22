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
 * @coversDefaultClass \phpDocumentor\Reflection\Types\ClassString
 */
class ClassStringTest extends TestCase
{
    /**
     * @dataProvider provideClassStrings
     * @covers ::__toString
     */
    public function testClassStringStringifyCorrectly(ClassString $array, string $expectedString) : void
    {
        $this->assertSame($expectedString, (string) $array);
    }

    /**
     * @return mixed[]
     */
    public function provideClassStrings() : array
    {
        return [
            'generic clss string' => [new ClassString(), 'class-string'],
            'typed class string' => [new ClassString(new Fqsen('\Foo\Bar')), 'class-string<\Foo\Bar>'],
        ];
    }
}
