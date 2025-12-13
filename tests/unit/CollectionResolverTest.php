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

use phpDocumentor\Reflection\PseudoTypes\Generic;
use phpDocumentor\Reflection\PseudoTypes\List_;
use phpDocumentor\Reflection\PseudoTypes\NonEmptyList;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CollectionResolverTest extends TestCase
{
    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\PseudoTypes\Generic
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingCollection(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('ArrayObject<string>', new Context(''));

        $this->assertInstanceOf(Generic::class, $resolvedType);
        $this->assertSame('\\ArrayObject<string>', (string) $resolvedType);
        $this->assertSame('\\ArrayObject', (string) $resolvedType->getFqsen());
        $this->assertEquals([new String_()], $resolvedType->getTypes());
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\PseudoTypes\Generic
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingCollectionWithKeyType(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('ArrayObject<string[],Iterator>', new Context(''));

        $this->assertInstanceOf(Generic::class, $resolvedType);
        $this->assertSame('\\ArrayObject<string[], \\Iterator>', (string) $resolvedType);
        $this->assertSame('\\ArrayObject', (string) $resolvedType->getFqsen());

        $types = $resolvedType->getTypes();

        $this->assertArrayHasKey(0, $types);
        $this->assertEquals(new Array_(new String_()), $types[0]);
        $this->assertArrayHasKey(1, $types);
        $this->assertEquals(new Object_(new Fqsen('\\Iterator')), $types[1]);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingArrayCollection(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('array<string>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('string[]', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(String_::class, $valueType);
        $this->assertInstanceOf(Compound::class, $keyType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingArrayCollectionWithKey(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('array<string, object|array>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<string, object|array>', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(String_::class, $keyType);
        $this->assertInstanceOf(Compound::class, $valueType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingArrayCollectionWithKeyAndWhitespace(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('array<string, object|array>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<string, object|array>', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(String_::class, $keyType);
        $this->assertInstanceOf(Compound::class, $valueType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\PseudoTypes\Generic
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingCollectionOfCollection(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('ArrayObject<string|integer|double,ArrayObject<DateTime>>', new Context(''));

        $this->assertInstanceOf(Generic::class, $resolvedType);
        $this->assertSame('\\ArrayObject<string|int|float, \\ArrayObject<\\DateTime>>', (string) $resolvedType);
        $this->assertSame('\\ArrayObject', (string) $resolvedType->getFqsen());

        $types = $resolvedType->getTypes();

        $this->assertArrayHasKey(0, $types);
        $this->assertEquals(new Compound([new String_(), new Integer(), new Float_()]), $types[0]);

        $this->assertArrayHasKey(1, $types);
        $this->assertInstanceOf(Generic::class, $types[1]);
        $this->assertSame('\\ArrayObject', (string) $types[1]->getFqsen());

        $nestedGenericTypes = $types[1]->getTypes();
        $this->assertArrayHasKey(0, $nestedGenericTypes);
        $this->assertEquals(new Object_(new Fqsen('\\DateTime')), $nestedGenericTypes[0]);
    }

    public function testGoodArrayCollectionKey(): void
    {
        $fixture = new TypeResolver();
        $resolvedType = $fixture->resolve('array<array-key, string>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<array-key, string>', (string) $resolvedType);

        $fixture = new TypeResolver();
        $resolvedType = $fixture->resolve('array<class-string, string>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<class-string, string>', (string) $resolvedType);
    }

    public function testMissingStartCollection(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unexpected token "<", expected type at offset 0');
        $fixture = new TypeResolver();
        $fixture->resolve('<string>', new Context(''));
    }

    public function testMissingEndCollection(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unexpected token "", expected \'>\' at offset 25');
        $fixture = new TypeResolver();
        $fixture->resolve('ArrayObject<object|string', new Context(''));
    }

    public function testBadCollectionClass(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('string is an unsupported generic');
        $fixture = new TypeResolver();
        $fixture->resolve('string<integer>', new Context(''));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingCollectionAsArray(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('array<string,float>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<string, float>', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(Float_::class, $valueType);
        $this->assertInstanceOf(String_::class, $keyType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingList(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('list<string>', new Context(''));

        $this->assertInstanceOf(List_::class, $resolvedType);
        $this->assertSame('list<string>', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(String_::class, $valueType);
        $this->assertInstanceOf(Integer::class, $keyType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingNonEmptyList(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('non-empty-list<string>', new Context(''));

        $this->assertInstanceOf(NonEmptyList::class, $resolvedType);
        $this->assertSame('non-empty-list<string>', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(String_::class, $valueType);
        $this->assertInstanceOf(Integer::class, $keyType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Nullable
     */
    public function testResolvingNullableArray(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('?array<int>', new Context(''));

        $this->assertInstanceOf(Nullable::class, $resolvedType);
        $this->assertSame('?int[]', (string) $resolvedType);
    }
}
