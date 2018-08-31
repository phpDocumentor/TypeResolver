<?php declare(strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;

/**
 * @covers ::<private>
 * @coversDefaultClass phpDocumentor\Reflection\TypeResolver
 */
class CollectionResolverTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::resolve
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingCollection()
    {
        $fixture = new TypeResolver();

        /** @var Collection $resolvedType */
        $resolvedType = $fixture->resolve('ArrayObject<string>', new Context(''));

        $this->assertInstanceOf(Collection::class, $resolvedType);
        $this->assertSame('\\ArrayObject<string>', (string) $resolvedType);

        $this->assertEquals('\\ArrayObject', (string) $resolvedType->getFqsen());

        /** @var Array_ $valueType */
        $valueType = $resolvedType->getValueType();

        /** @var Compound $keyType */
        $keyType = $resolvedType->getKeyType();

        $this->assertInstanceOf(Types\String_::class, $valueType);
        $this->assertInstanceOf(Types\Compound::class, $keyType);
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingCollectionWithKeyType()
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
     * @covers ::__construct
     * @covers ::resolve
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingArrayCollection()
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
     * @covers ::__construct
     * @covers ::resolve
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingArrayCollectionWithKey()
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
     * @covers ::__construct
     * @covers ::resolve
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingArrayCollectionWithKeyAndWhitespace()
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
     * @covers ::__construct
     * @covers ::resolve
     * 
     * @expectedException \InvalidArgumentException
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingArrayCollectionWithKeyAndTooManyWhitespace()
    {
        $fixture = new TypeResolver();

        /** @var Collection $resolvedType */
        $resolvedType = $fixture->resolve('array<string,  object|array>', new Context(''));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingCollectionOfCollection()
    {
        $fixture = new TypeResolver();

        /** @var Collection $resolvedType */
        $resolvedType = $fixture->resolve('ArrayObject<string|integer|double,ArrayObject<DateTime>>', new Context(''));

        $this->assertInstanceOf(Collection::class, $resolvedType);
        $this->assertSame('\\ArrayObject<string|int|float,\\ArrayObject<\\DateTime>>', (string) $resolvedType);

        $this->assertEquals('\\ArrayObject', (string) $resolvedType->getFqsen());

        /** @var Collection $valueType */
        $valueType = $resolvedType->getValueType();
        $this->assertInstanceOf(Types\Collection::class, $valueType);
        $this->assertInstanceOf(Types\Object_::class, $valueType->getValueType());
        $this->assertEquals('\\ArrayObject', (string) $valueType->getFqsen());
        $this->assertEquals('\\DateTime', (string) $valueType->getValueType()->getFqsen());

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
     * @expectedException \RuntimeException
     * @expectedExceptionMessage An array can have only integers or strings as keys
     */
    public function testBadArrayCollectionKey()
    {
        $fixture = new TypeResolver();
        $fixture->resolve('array<object,string>', new Context(''));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unexpected collection operator "<", class name is missing
     */
    public function testMissingStartCollection()
    {
        $fixture = new TypeResolver();
        $fixture->resolve('<string>', new Context(''));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Collection: ">" is missing
     */
    public function testMissingEndCollection()
    {
        $fixture = new TypeResolver();
        $fixture->resolve('ArrayObject<object|string', new Context(''));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @expectedException \RuntimeException
     * @expectedExceptionMessage string is not a collection
     */
    public function testBadCollectionClass()
    {
        $fixture = new TypeResolver();
        $fixture->resolve('string<integer>', new Context(''));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingCollectionAsArray()
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
