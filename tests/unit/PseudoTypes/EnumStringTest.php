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
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

class EnumStringTest extends TestCase
{
    public function testCreate(): void
    {
        $genericType = new Object_(new Fqsen('\Foo\Bar'));
        $type = new EnumString($genericType);

        $this->assertSame($genericType, $type->getGenericType());
        $this->assertEquals(new String_(), $type->underlyingType());
    }

    /**
     * @dataProvider provideToStringData
     */
    public function testToString(EnumString $type, string $expectedString): void
    {
        $this->assertSame($expectedString, (string) $type);
    }

    /**
     * @return array<string, array{EnumString, string}>
     */
    public function provideToStringData(): array
    {
        return [
            'basic' => [new EnumString(), 'enum-string'],
            'with generics' => [
                new EnumString(new Object_(new Fqsen('\Foo\Bar'))),
                'enum-string<\Foo\Bar>',
            ],
            'more than one enum' => [
                new EnumString(
                    new Compound([
                        new Object_(new Fqsen('\Foo\Bar')),
                        new Object_(new Fqsen('\Foo\Barrr')),
                    ])
                ),
                'enum-string<\Foo\Bar|\Foo\Barrr>',
            ],
        ];
    }
}
