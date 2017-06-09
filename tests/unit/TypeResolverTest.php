<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

use Mockery as m;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use Mockery\MockInterface;
use phpDocumentor\Reflection\Types\String_;

/**
 * @coversDefaultClass phpDocumentor\Reflection\TypeResolver
 */
class TypeResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $keyword
     * @param string $expectedClass
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Object_
     *
     * @dataProvider provideKeywords
     */
    public function testResolvingKeywords($keyword, $expectedClass)
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve($keyword, new Context(''));

        $this->assertInstanceOf($expectedClass, $resolvedType);
    }

    /**
     * @param string $fqsen
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @dataProvider provideFqcn
     */
    public function testResolvingFQSENs($fqsen)
    {
        $fixture = new TypeResolver();

        /** @var Object_ $resolvedType */
        $resolvedType = $fixture->resolve($fqsen, new Context(''));

        $this->assertInstanceOf(Object_::class, $resolvedType);
        $this->assertInstanceOf(Fqsen::class, $resolvedType->getFqsen());
        $this->assertSame($fqsen, (string)$resolvedType);
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     */
    public function testResolvingRelativeQSENsBasedOnNamespace()
    {
        $fixture = new TypeResolver();

        /** @var Object_ $resolvedType */
        $resolvedType = $fixture->resolve('DocBlock', new Context('phpDocumentor\Reflection'));

        $this->assertInstanceOf(Object_::class, $resolvedType);
        $this->assertInstanceOf(Fqsen::class, $resolvedType->getFqsen());
        $this->assertSame('\phpDocumentor\Reflection\DocBlock', (string)$resolvedType);
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     */
    public function testResolvingRelativeQSENsBasedOnNamespaceAlias()
    {
        $fixture = new TypeResolver();

        /** @var Object_ $resolvedType */
        $resolvedType = $fixture->resolve(
            'm\MockInterface',
            new Context('phpDocumentor\Reflection', ['m' => m::class])
        );

        $this->assertInstanceOf(Object_::class, $resolvedType);
        $this->assertInstanceOf(Fqsen::class, $resolvedType->getFqsen());
        $this->assertSame('\Mockery\MockInterface', (string)$resolvedType);
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingTypedArrays()
    {
        $fixture = new TypeResolver();

        /** @var Array_ $resolvedType */
        $resolvedType = $fixture->resolve('string[]', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('string[]', (string)$resolvedType);
        $this->assertInstanceOf(Compound::class, $resolvedType->getKeyType());
        $this->assertInstanceOf(Types\String_::class, $resolvedType->getValueType());
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Nullable
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingNullableTypes()
    {
        $fixture = new TypeResolver();

        /** @var Nullable $resolvedType */
        $resolvedType = $fixture->resolve('?string', new Context(''));

        $this->assertInstanceOf(Nullable::class, $resolvedType);
        $this->assertInstanceOf(String_::class, $resolvedType->getActualType());
        $this->assertSame('?string', (string)$resolvedType);
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses phpDocumentor\Reflection\Types\Context
     * @uses phpDocumentor\Reflection\Types\Array_
     * @uses phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingNestedTypedArrays()
    {
        $fixture = new TypeResolver();

        /** @var Array_ $resolvedType */
        $resolvedType = $fixture->resolve('string[][]', new Context(''));

        /** @var Array_ $childValueType */
        $childValueType = $resolvedType->getValueType();

        $this->assertInstanceOf(Array_::class, $resolvedType);

        $this->assertSame('string[][]', (string)$resolvedType);
        $this->assertInstanceOf(Compound::class, $resolvedType->getKeyType());
        $this->assertInstanceOf(Array_::class, $childValueType);

        $this->assertSame('string[]', (string)$childValueType);
        $this->assertInstanceOf(Compound::class, $childValueType->getKeyType());
        $this->assertInstanceOf(Types\String_::class, $childValueType->getValueType());
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\String_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     */
    public function testResolvingCompoundTypes()
    {
        $fixture = new TypeResolver();

        /** @var Compound $resolvedType */
        $resolvedType = $fixture->resolve('string|Reflection\DocBlock', new Context('phpDocumentor'));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('string|\phpDocumentor\Reflection\DocBlock', (string)$resolvedType);

        /** @var String $secondType */
        $firstType = $resolvedType->get(0);

        /** @var Object_ $secondType */
        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf(Types\String_::class, $firstType);
        $this->assertInstanceOf(Object_::class, $secondType);
        $this->assertInstanceOf(Fqsen::class, $secondType->getFqsen());
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     */
    public function testResolvingCompoundTypedArrayTypes()
    {
        $fixture = new TypeResolver();

        /** @var Compound $resolvedType */
        $resolvedType = $fixture->resolve('\stdClass[]|Reflection\DocBlock[]', new Context('phpDocumentor'));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('\stdClass[]|\phpDocumentor\Reflection\DocBlock[]', (string)$resolvedType);

        /** @var Array_ $secondType */
        $firstType = $resolvedType->get(0);

        /** @var Array_ $secondType */
        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf(Array_::class, $firstType);
        $this->assertInstanceOf(Array_::class, $secondType);
        $this->assertInstanceOf(Object_::class, $firstType->getValueType());
        $this->assertInstanceOf(Object_::class, $secondType->getValueType());
    }

    /**
     * This test asserts that the parameter order is correct.
     *
     * When you pass two arrays separated by the compound operator (i.e. 'integer[]|string[]') then we always split the
     * expression in its compound parts and then we parse the types with the array operators. If we were to switch the
     * order around then 'integer[]|string[]' would read as an array of string or integer array; which is something
     * other than what we intend.
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Integer
     * @uses \phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingCompoundTypesWithTwoArrays()
    {
        $fixture = new TypeResolver();

        /** @var Compound $resolvedType */
        $resolvedType = $fixture->resolve('integer[]|string[]', new Context(''));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('int[]|string[]', (string)$resolvedType);

        /** @var Array_ $firstType */
        $firstType = $resolvedType->get(0);

        /** @var Array_ $secondType */
        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf(Array_::class, $firstType);
        $this->assertInstanceOf(Types\Integer::class, $firstType->getValueType());
        $this->assertInstanceOf(Array_::class, $secondType);
        $this->assertInstanceOf(Types\String_::class, $secondType->getValueType());
    }

    /**
     * @covers ::__construct
     * @covers ::addKeyword
     * @uses \phpDocumentor\Reflection\TypeResolver::resolve
     * @uses \phpDocumentor\Reflection\TypeResolver::<private>
     * @uses \phpDocumentor\Reflection\Types\Context
     */
    public function testAddingAKeyword()
    {
        // Assign
        $typeMock = m::mock(Type::class);

        // Act
        $fixture = new TypeResolver();
        $fixture->addKeyword('mock', get_class($typeMock));

        // Assert
        $result = $fixture->resolve('mock', new Context(''));
        $this->assertInstanceOf(get_class($typeMock), $result);
        $this->assertNotSame($typeMock, $result);
    }

    /**
     * @covers ::__construct
     * @covers ::addKeyword
     * @uses \phpDocumentor\Reflection\Types\Context
     * @expectedException \InvalidArgumentException
     */
    public function testAddingAKeywordFailsIfTypeClassDoesNotExist()
    {
        $fixture = new TypeResolver();
        $fixture->addKeyword('mock', 'IDoNotExist');
    }

    /**
     * @covers ::__construct
     * @covers ::addKeyword
     * @uses \phpDocumentor\Reflection\Types\Context
     * @expectedException \InvalidArgumentException
     */
    public function testAddingAKeywordFailsIfTypeClassDoesNotImplementTypeInterface()
    {
        $fixture = new TypeResolver();
        $fixture->addKeyword('mock', \stdClass::class);
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @uses \phpDocumentor\Reflection\Types\Context
     *
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionIsThrownIfTypeIsEmpty()
    {
        $fixture = new TypeResolver();
        $fixture->resolve(' ', new Context(''));
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @uses \phpDocumentor\Reflection\Types\Context
     *
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionIsThrownIfTypeIsNotAString()
    {
        $fixture = new TypeResolver();
        $fixture->resolve(['a'], new Context(''));
    }

    /**
     * Returns a list of keywords and expected classes that are created from them.
     *
     * @return string[][]
     */
    public function provideKeywords()
    {
        return [
            ['string', Types\String_::class],
            ['int', Types\Integer::class],
            ['integer', Types\Integer::class],
            ['float', Types\Float_::class],
            ['double', Types\Float_::class],
            ['bool', Types\Boolean::class],
            ['boolean', Types\Boolean::class],
            ['resource', Types\Resource_::class],
            ['null', Types\Null_::class],
            ['callable', Types\Callable_::class],
            ['callback', Types\Callable_::class],
            ['array', Array_::class],
            ['scalar', Types\Scalar::class],
            ['object', Object_::class],
            ['mixed', Types\Mixed_::class],
            ['void', Types\Void_::class],
            ['$this', Types\This::class],
            ['static', Types\Static_::class],
            ['self', Types\Self_::class],
            ['parent', Types\Parent_::class],
            ['iterable', Iterable_::class],
        ];
    }

    /**
     * Provides a list of FQSENs to test the resolution patterns with.
     *
     * @return string[][]
     */
    public function provideFqcn()
    {
        return [
            'namespace' => ['\phpDocumentor\Reflection'],
            'class'     => ['\phpDocumentor\Reflection\DocBlock'],
        ];
    }
}
