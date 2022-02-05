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

namespace phpDocumentor\Reflection;

use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\ClassString;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Integer;
use PHPUnit\Framework\TestCase;

/**
 * @covers ::<private>
 * @coversDefaultClass \phpDocumentor\Reflection\TypeResolver
 */
class ClassStringResolverTest extends TestCase
{
    /**
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Integer
     * @uses \phpDocumentor\Reflection\Types\ClassString
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolvingClassString(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('array<int,class-string<\Foo\Bar>>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<int,class-string<\Foo\Bar>>', (string) $resolvedType);

        $keyType = $resolvedType->getKeyType();
        $valueTpye = $resolvedType->getValueType();

        $this->assertInstanceOf(Integer::class, $keyType);

        $this->assertInstanceOf(ClassString::class, $valueTpye);
        $this->assertSame('Bar', $valueTpye->getFqsen()->getName());
        $this->assertSame('\Foo\Bar', $valueTpye->getFqsen()->__toString());
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Integer
     * @uses \phpDocumentor\Reflection\Types\ClassString
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolvingClassStrings(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('array<int,class-string<\Foo\Bar|\Foo\Lall>>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<int,class-string<\Foo\Bar|\Foo\Lall>>', (string) $resolvedType);

        $keyType = $resolvedType->getKeyType();
        $valueTpye = $resolvedType->getValueType();

        $this->assertInstanceOf(Integer::class, $keyType);

        $this->assertInstanceOf(Compound::class, $valueTpye);
        foreach ($valueTpye->getIterator() as $type) {
            $this->assertInstanceOf(ClassString::class, $type);
        }
    }
}
