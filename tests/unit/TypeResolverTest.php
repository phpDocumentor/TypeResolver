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
use Mockery as m;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;
use stdClass;
use function get_class;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\TypeResolver
 */
class TypeResolverTest extends TestCase
{
    /**
     * Call Mockery::close after each test.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * @uses         \phpDocumentor\Reflection\Types\Context
     * @uses         \phpDocumentor\Reflection\Types\Array_
     * @uses         \phpDocumentor\Reflection\Types\Object_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @dataProvider provideKeywords
     */
    public function testResolvingKeywords(string $keyword, string $expectedClass) : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve($keyword, new Context(''));

        $this->assertInstanceOf($expectedClass, $resolvedType);
    }

    /**
     * @uses         \phpDocumentor\Reflection\Types\Context
     * @uses         \phpDocumentor\Reflection\Types\Object_
     * @uses         \phpDocumentor\Reflection\Fqsen
     * @uses         \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @dataProvider provideFqcn
     */
    public function testResolvingFQSENs(string $fqsen) : void
    {
        $fixture = new TypeResolver();

        /** @var Object_ $resolvedType */
        $resolvedType = $fixture->resolve($fqsen, new Context(''));

        $this->assertInstanceOf(Object_::class, $resolvedType);
        $this->assertInstanceOf(Fqsen::class, $resolvedType->getFqsen());
        $this->assertSame($fqsen, (string) $resolvedType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingRelativeQSENsBasedOnNamespace() : void
    {
        $fixture = new TypeResolver();

        /** @var Object_ $resolvedType */
        $resolvedType = $fixture->resolve('DocBlock', new Context('phpDocumentor\Reflection'));

        $this->assertInstanceOf(Object_::class, $resolvedType);
        $this->assertInstanceOf(Fqsen::class, $resolvedType->getFqsen());
        $this->assertSame('\phpDocumentor\Reflection\DocBlock', (string) $resolvedType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingRelativeQSENsBasedOnNamespaceAlias() : void
    {
        $fixture = new TypeResolver();

        /** @var Object_ $resolvedType */
        $resolvedType = $fixture->resolve(
            'm\MockInterface',
            new Context('phpDocumentor\Reflection', ['m' => m::class])
        );

        $this->assertInstanceOf(Object_::class, $resolvedType);
        $this->assertInstanceOf(Fqsen::class, $resolvedType->getFqsen());
        $this->assertSame('\Mockery\MockInterface', (string) $resolvedType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingTypedArrays() : void
    {
        $fixture = new TypeResolver();

        /** @var Array_ $resolvedType */
        $resolvedType = $fixture->resolve('string[]', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('string[]', (string) $resolvedType);
        $this->assertInstanceOf(Compound::class, $resolvedType->getKeyType());
        $this->assertInstanceOf(Types\String_::class, $resolvedType->getValueType());
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Nullable
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingNullableTypes() : void
    {
        $fixture = new TypeResolver();

        /** @var Nullable $resolvedType */
        $resolvedType = $fixture->resolve('?string', new Context(''));

        $this->assertInstanceOf(Nullable::class, $resolvedType);
        $this->assertInstanceOf(String_::class, $resolvedType->getActualType());
        $this->assertSame('?string', (string) $resolvedType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingNestedTypedArrays() : void
    {
        $fixture = new TypeResolver();

        /** @var Array_ $resolvedType */
        $resolvedType = $fixture->resolve('string[][]', new Context(''));

        /** @var Array_ $childValueType */
        $childValueType = $resolvedType->getValueType();

        $this->assertInstanceOf(Array_::class, $resolvedType);

        $this->assertSame('string[][]', (string) $resolvedType);
        $this->assertInstanceOf(Compound::class, $resolvedType->getKeyType());
        $this->assertInstanceOf(Array_::class, $childValueType);

        $this->assertSame('string[]', (string) $childValueType);
        $this->assertInstanceOf(Compound::class, $childValueType->getKeyType());
        $this->assertInstanceOf(Types\String_::class, $childValueType->getValueType());
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\String_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingCompoundTypes() : void
    {
        $fixture = new TypeResolver();

        /** @var Compound $resolvedType */
        $resolvedType = $fixture->resolve('string|Reflection\DocBlock', new Context('phpDocumentor'));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('string|\phpDocumentor\Reflection\DocBlock', (string) $resolvedType);

        /** @var string $secondType */
        $firstType = $resolvedType->get(0);

        /** @var Object_ $secondType */
        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf(Types\String_::class, $firstType);
        $this->assertInstanceOf(Object_::class, $secondType);
        $this->assertInstanceOf(Fqsen::class, $secondType->getFqsen());
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingCompoundTypedArrayTypes() : void
    {
        $fixture = new TypeResolver();

        /** @var Compound $resolvedType */
        $resolvedType = $fixture->resolve('\stdClass[]|Reflection\DocBlock[]', new Context('phpDocumentor'));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('\stdClass[]|\phpDocumentor\Reflection\DocBlock[]', (string) $resolvedType);

        /** @var Array_ $firstType */
        $firstType = $resolvedType->get(0);

        /** @var Array_ $secondType */
        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf(Array_::class, $firstType);
        $this->assertInstanceOf(Array_::class, $secondType);
        $this->assertInstanceOf(Object_::class, $firstType->getValueType());
        $this->assertInstanceOf(Object_::class, $secondType->getValueType());
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\String_
     * @uses \phpDocumentor\Reflection\Types\Nullable
     * @uses \phpDocumentor\Reflection\Types\Null_
     * @uses \phpDocumentor\Reflection\Types\Boolean
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingNullableCompoundTypes() : void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('?string|null|?boolean');

        $this->assertSame('?string|null|?bool', (string) $resolvedType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingArrayExpressionObjectsTypes() : void
    {
        $fixture = new TypeResolver();

        /** @var Array_ $resolvedType */
        $resolvedType = $fixture->resolve('(\stdClass|Reflection\DocBlock)[]', new Context('phpDocumentor'));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('(\stdClass|\phpDocumentor\Reflection\DocBlock)[]', (string) $resolvedType);

        /** @var Compound $valueType */
        $valueType = $resolvedType->getValueType();

        $this->assertInstanceOf(Compound::class, $valueType);

        /** @var Object_ $firstType */
        $firstType = $valueType->get(0);

        /** @var Object_ $secondType */
        $secondType = $valueType->get(1);

        $this->assertInstanceOf(Object_::class, $firstType);
        $this->assertInstanceOf(Object_::class, $secondType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingArrayExpressionSimpleTypes() : void
    {
        $fixture = new TypeResolver();

        /** @var Array_ $resolvedType */
        $resolvedType = $fixture->resolve('(string|\stdClass|boolean)[]', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('(string|\stdClass|bool)[]', (string) $resolvedType);

        /** @var Compound $valueType */
        $valueType = $resolvedType->getValueType();

        $this->assertInstanceOf(Compound::class, $valueType);

        /** @var String_ $firstType */
        $firstType = $valueType->get(0);

        /** @var Object_ $secondType */
        $secondType = $valueType->get(1);

        /** @var Boolean $thirdType */
        $thirdType = $valueType->get(2);

        $this->assertInstanceOf(String_::class, $firstType);
        $this->assertInstanceOf(Object_::class, $secondType);
        $this->assertInstanceOf(Boolean::class, $thirdType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingArrayOfArrayExpressionTypes() : void
    {
        $fixture = new TypeResolver();

        /** @var Array_ $resolvedType */
        $resolvedType = $fixture->resolve('(string|\stdClass)[][]', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('(string|\stdClass)[][]', (string) $resolvedType);

        /** @var Array_ $parentArrayType */
        $parentArrayType = $resolvedType->getValueType();
        $this->assertInstanceOf(Array_::class, $parentArrayType);

        /** @var Compound $valueType */
        $valueType = $parentArrayType->getValueType();
        $this->assertInstanceOf(Compound::class, $valueType);

        /** @var String_ $firstType */
        $firstType = $valueType->get(0);

        /** @var Object_ $secondType */
        $secondType = $valueType->get(1);

        $this->assertInstanceOf(String_::class, $firstType);
        $this->assertInstanceOf(Object_::class, $secondType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testReturnEmptyCompoundOnAnUnclosedArrayExpressionType() : void
    {
        $fixture = new TypeResolver();

        /** @var Compound $resolvedType */
        $resolvedType = $fixture->resolve('(string|\stdClass', new Context(''));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('', (string) $resolvedType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingArrayExpressionOrCompoundTypes() : void
    {
        $fixture = new TypeResolver();

        /** @var Compound $resolvedType */
        $resolvedType = $fixture->resolve('\stdClass|(string|\stdClass)[]|bool', new Context(''));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('\stdClass|(string|\stdClass)[]|bool', (string) $resolvedType);

        /** @var Object_ $firstType */
        $firstType = $resolvedType->get(0);
        $this->assertInstanceOf(Object_::class, $firstType);

        /** @var Array_ $secondType */
        $secondType = $resolvedType->get(1);
        $this->assertInstanceOf(Array_::class, $secondType);

        /** @var Array_ $thirdType */
        $thirdType = $resolvedType->get(2);
        $this->assertInstanceOf(Boolean::class, $thirdType);

        /** @var Compound $valueType */
        $valueType = $secondType->getValueType();
        $this->assertInstanceOf(Compound::class, $valueType);

        /** @var String_ $firstArrayType */
        $firstArrayType = $valueType->get(0);

        /** @var Object_ $secondArrayType */
        $secondArrayType = $valueType->get(1);

        $this->assertInstanceOf(String_::class, $firstArrayType);
        $this->assertInstanceOf(Object_::class, $secondArrayType);
    }

    /**
     * This test asserts that the parameter order is correct.
     *
     * When you pass two arrays separated by the compound operator (i.e. 'integer[]|string[]') then we always split the
     * expression in its compound parts and then we parse the types with the array operators. If we were to switch the
     * order around then 'integer[]|string[]' would read as an array of string or integer array; which is something
     * other than what we intend.
     *
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Integer
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingCompoundTypesWithTwoArrays() : void
    {
        $fixture = new TypeResolver();

        /** @var Compound $resolvedType */
        $resolvedType = $fixture->resolve('integer[]|string[]', new Context(''));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('int[]|string[]', (string) $resolvedType);

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
     * @uses \phpDocumentor\Reflection\TypeResolver::resolve
     * @uses \phpDocumentor\Reflection\TypeResolver::<private>
     * @uses \phpDocumentor\Reflection\Types\Context
     *
     * @covers ::__construct
     * @covers ::addKeyword
     */
    public function testAddingAKeyword() : void
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
     * @uses \phpDocumentor\Reflection\Types\Context
     *
     * @covers ::__construct
     * @covers ::addKeyword
     *
     * @expectedException InvalidArgumentException
     */
    public function testAddingAKeywordFailsIfTypeClassDoesNotExist() : void
    {
        $fixture = new TypeResolver();
        $fixture->addKeyword('mock', 'IDoNotExist');
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     *
     * @covers ::__construct
     * @covers ::addKeyword
     *
     * @expectedException InvalidArgumentException
     */
    public function testAddingAKeywordFailsIfTypeClassDoesNotImplementTypeInterface() : void
    {
        $fixture = new TypeResolver();
        $fixture->addKeyword('mock', stdClass::class);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     *
     * @covers ::__construct
     * @covers ::resolve
     *
     * @expectedException InvalidArgumentException
     */
    public function testExceptionIsThrownIfTypeIsEmpty() : void
    {
        $fixture = new TypeResolver();
        $fixture->resolve(' ', new Context(''));
    }

    /**
     * Returns a list of keywords and expected classes that are created from them.
     *
     * @return string[][]
     */
    public function provideKeywords() : array
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
    public function provideFqcn() : array
    {
        return [
            'namespace' => ['\phpDocumentor\Reflection'],
            'class' => ['\phpDocumentor\Reflection\DocBlock'],
        ];
    }
}
