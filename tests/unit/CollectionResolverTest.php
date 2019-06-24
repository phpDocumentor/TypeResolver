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

use InvalidArgumentException;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;
use RuntimeException;

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

        /** @var Collection $resolvedType */
        $resolvedType = $fixture->resolve('ArrayObject<string>', new Context(''));

        $this->assertInstanceOf(Collection::class, $resolvedType);
        $this->assertSame('\\ArrayObject<string>', (string) $resolvedType);

        $this->assertEquals('\\ArrayObject', (string) $resolvedType->getFqsen());

        /** @var String_ $valueType */
        $valueType = $resolvedType->getValueType();

        /** @var Compound $keyType */
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

        /** @var Collection $resolvedType */
        $resolvedType = $fixture->resolve('ArrayObject<string[],Iterator>', new Context(''));

        $this->assertInstanceOf(Collection::class, $resolvedType);
        $this->assertSame('\\ArrayObject<string[],\\Iterator>', (string) $resolvedType);

        $this->assertEquals('\\ArrayObject', (string) $resolvedType->getFqsen());

        /** @var Object_ $valueType */
        $valueType = $resolvedType->getValueType();

        /** @var Array_ $keyType */
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

        /** @var Collection $resolvedType */
        $resolvedType = $fixture->resolve('array<string>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('string[]', (string) $resolvedType);

        /** @var Array_ $valueType */
        $valueType = $resolvedType->getValueType();

        /** @var Compound $keyType */
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

        /** @var Collection $resolvedType */
        $resolvedType = $fixture->resolve('array<string,object|array>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<string,object|array>', (string) $resolvedType);

        /** @var Array_ $valueType */
        $valueType = $resolvedType->getValueType();

        /** @var Compound $keyType */
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

        /** @var Collection $resolvedType */
        $resolvedType = $fixture->resolve('array<string, object|array>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<string,object|array>', (string) $resolvedType);

        /** @var Array_ $valueType */
        $valueType = $resolvedType->getValueType();

        /** @var Compound $keyType */
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
     *
     * @expectedException InvalidArgumentException
     */
    public function testResolvingArrayCollectionWithKeyAndTooManyWhitespace() : void
    {
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

        /** @var Collection $resolvedType */
        $resolvedType = $fixture->resolve('ArrayObject<string|integer|double,ArrayObject<DateTime>>', new Context(''));

        $this->assertInstanceOf(Collection::class, $resolvedType);
        $this->assertSame('\\ArrayObject<string|int|float,\\ArrayObject<\\DateTime>>', (string) $resolvedType);

        $this->assertEquals('\\ArrayObject', (string) $resolvedType->getFqsen());

        /** @var Collection $valueType */
        $valueType = $resolvedType->getValueType();
        /** @var Object_ $collectionValueType */
        $collectionValueType = $valueType->getValueType();
        $this->assertInstanceOf(Types\Collection::class, $valueType);
        $this->assertInstanceOf(Types\Object_::class, $valueType->getValueType());
        $this->assertEquals('\\ArrayObject', (string) $valueType->getFqsen());
        $this->assertEquals('\\DateTime', (string) $collectionValueType->getFqsen());

        /** @var Compound $keyType */
        $keyType = $resolvedType->getKeyType();
        $this->assertInstanceOf(Types\Compound::class, $keyType);
        $this->assertInstanceOf(Types\String_::class, $keyType->get(0));
        $this->assertInstanceOf(Types\Integer::class, $keyType->get(1));
        $this->assertInstanceOf(Types\Float_::class, $keyType->get(2));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @expectedException RuntimeException
     * @expectedExceptionMessage An array can have only integers or strings as keys
     */
    public function testBadArrayCollectionKey() : void
    {
        $fixture = new TypeResolver();
        $fixture->resolve('array<object,string>', new Context(''));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unexpected collection operator "<", class name is missing
     */
    public function testMissingStartCollection() : void
    {
        $fixture = new TypeResolver();
        $fixture->resolve('<string>', new Context(''));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @expectedException RuntimeException
     * @expectedExceptionMessage Collection: ">" is missing
     */
    public function testMissingEndCollection() : void
    {
        $fixture = new TypeResolver();
        $fixture->resolve('ArrayObject<object|string', new Context(''));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @expectedException RuntimeException
     * @expectedExceptionMessage string is not a collection
     */
    public function testBadCollectionClass() : void
    {
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

        /** @var Collection $resolvedType */
        $resolvedType = $fixture->resolve('array<string,float>', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('array<string,float>', (string) $resolvedType);

        /** @var Array_ $valueType */
        $valueType = $resolvedType->getValueType();

        /** @var Compound $keyType */
        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(Types\Float_::class, $valueType);
        $this->assertInstanceOf(Types\String_::class, $keyType);
    }
}
