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

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;

class ClassStringTest extends TestCase
{
    /**
     * @dataProvider provideClassStrings
     */
    public function testClassStringStringifyCorrectly(ClassString $type, string $expectedString): void
    {
        $this->assertSame($expectedString, (string) $type);
    }

    /**
     * @return array<string, array{ClassString, string}>
     */
    public function provideClassStrings(): array
    {
        return [
            'generic class string' => [new ClassString(), 'class-string'],
            'typed class string' => [
                new ClassString(new Object_(new Fqsen('\Foo\Bar'))),
                'class-string<\Foo\Bar>',
            ],
            'more than one class' => [
                new ClassString(
                    new Compound([
                        new Object_(new Fqsen('\Foo\Bar')),
                        new Object_(new Fqsen('\Foo\Barrr')),
                    ])
                ),
                'class-string<\Foo\Bar|\Foo\Barrr>',
            ],
        ];
    }
}
