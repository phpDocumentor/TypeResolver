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
 * @coversDefaultClass \phpDocumentor\Reflection\Types\InterfaceString
 */
class InterfaceStringTest extends TestCase
{
    /**
     * @dataProvider provideInterfaceStrings
     * @covers ::__toString
     */
    public function testInterfaceStringStringifyCorrectly(InterfaceString $array, string $expectedString): void
    {
        $this->assertSame($expectedString, (string) $array);
    }

    /**
     * @return mixed[]
     */
    public function provideInterfaceStrings(): array
    {
        return [
            'generic interface string' => [new InterfaceString(), 'interface-string'],
            'typed interface string' => [new InterfaceString(new Fqsen('\Foo\Bar')), 'interface-string<\Foo\Bar>'],
        ];
    }
}
