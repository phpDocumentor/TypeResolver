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
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Context;
use PHPUnit\Framework\TestCase;

/**
 * @covers ::<private>
 * @coversDefaultClass \phpDocumentor\Reflection\TypeResolver
 */
class CollectionResolverTest extends TestCase
{
    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::resolve
     * @covers ::__construct
     */
    public function testResolvingCollection() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('ArrayObject<string>', new Context(''));

        $this->assertInstanceOf(Collection::class, $resolvedType);
        $this->assertSame('\\ArrayObject<string>', (string) $resolvedType);

        $this->assertEquals('\\ArrayObject', (string) $resolvedType->getFqsen());

        $valueType = $resolvedType->getValueType();

        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(Types\String_::class, $valueType);
        $this->assertInstanceOf(Types\Compound::class, $keyType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolvingCollectionWithKeyType() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('ArrayObject<string[],Iterator>', new Context(''));

        $this->assertInstanceOf(Collection::class, $resolvedType);
        $this->assertSame('\\ArrayObject<string[],\\Iterator>', (string) $resolvedType);

        $this->assertEquals('\\ArrayObject', (string) $resolvedType->getFqsen());

        $valueType = $resolvedType->getValueType();

        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(Types\Object_::class, $valueType);
        $this->assertEquals('\\Iterator', (string) $valueType->getFqsen());
        $this->assertInstanceOf(Types\Array_::class, $keyType);
        $this->assertInstanceOf(Types\String_::class, $keyType->getValueType());
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolvingArrayCollection() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('array<string>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('string[]', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(Types\String_::class, $valueType);
        $this->assertInstanceOf(Types\Compound::class, $keyType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolvingArrayCollectionWithKey() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('array<string,object|array>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<string,object|array>', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(Types\String_::class, $keyType);
        $this->assertInstanceOf(Types\Compound::class, $valueType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_

     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolvingArrayCollectionWithKeyAndWhitespace() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('array<string, object|array>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<string,object|array>', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(Types\String_::class, $keyType);
        $this->assertInstanceOf(Types\Compound::class, $valueType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolvingArrayCollectionWithKeyAndTooManyWhitespace() : void
    {
        $this->expectException('InvalidArgumentException');
        $fixture = new TypeResolver();

        $fixture->resolve('array<string,  object|array>', new Context(''));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolvingCollectionOfCollection() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('ArrayObject<string|integer|double,ArrayObject<DateTime>>', new Context(''));

        $this->assertInstanceOf(Collection::class, $resolvedType);
        $this->assertSame('\\ArrayObject<string|int|float,\\ArrayObject<\\DateTime>>', (string) $resolvedType);

        $this->assertEquals('\\ArrayObject', (string) $resolvedType->getFqsen());

        $valueType = $resolvedType->getValueType();
        $collectionValueType = $valueType->getValueType();
        $this->assertInstanceOf(Types\Collection::class, $valueType);
        $this->assertInstanceOf(Types\Object_::class, $valueType->getValueType());
        $this->assertEquals('\\ArrayObject', (string) $valueType->getFqsen());
        $this->assertEquals('\\DateTime', (string) $collectionValueType->getFqsen());

        $keyType = $resolvedType->getKeyType();
        $this->assertInstanceOf(Types\Compound::class, $keyType);
        $this->assertInstanceOf(Types\String_::class, $keyType->get(0));
        $this->assertInstanceOf(Types\Integer::class, $keyType->get(1));
        $this->assertInstanceOf(Types\Float_::class, $keyType->get(2));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testBadArrayCollectionKey() : void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('An array can have only integers or strings as keys');
        $fixture = new TypeResolver();
        $fixture->resolve('array<object,string>', new Context(''));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testMissingStartCollection() : void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Unexpected collection operator "<", class name is missing');
        $fixture = new TypeResolver();
        $fixture->resolve('<string>', new Context(''));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testMissingEndCollection() : void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Collection: ">" is missing');
        $fixture = new TypeResolver();
        $fixture->resolve('ArrayObject<object|string', new Context(''));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testBadCollectionClass() : void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('string is not a collection');
        $fixture = new TypeResolver();
        $fixture->resolve('string<integer>', new Context(''));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolvingCollectionAsArray() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('array<string,float>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<string,float>', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(Types\Float_::class, $valueType);
        $this->assertInstanceOf(Types\String_::class, $keyType);
    }
}
