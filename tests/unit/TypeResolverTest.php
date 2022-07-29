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
use phpDocumentor\Reflection\PseudoTypes\CallableString;
use phpDocumentor\Reflection\PseudoTypes\False_;
use phpDocumentor\Reflection\PseudoTypes\HtmlEscapedString;
use phpDocumentor\Reflection\PseudoTypes\List_;
use phpDocumentor\Reflection\PseudoTypes\LiteralString;
use phpDocumentor\Reflection\PseudoTypes\LowercaseString;
use phpDocumentor\Reflection\PseudoTypes\NegativeInteger;
use phpDocumentor\Reflection\PseudoTypes\NonEmptyLowercaseString;
use phpDocumentor\Reflection\PseudoTypes\NonEmptyString;
use phpDocumentor\Reflection\PseudoTypes\Numeric_;
use phpDocumentor\Reflection\PseudoTypes\NumericString;
use phpDocumentor\Reflection\PseudoTypes\PositiveInteger;
use phpDocumentor\Reflection\PseudoTypes\TraitString;
use phpDocumentor\Reflection\PseudoTypes\True_;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\ArrayKey;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\ClassString;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Expression;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\InterfaceString;
use phpDocumentor\Reflection\Types\Intersection;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Never_;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Parent_;
use phpDocumentor\Reflection\Types\Resource_;
use phpDocumentor\Reflection\Types\Scalar;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\Static_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\This;
use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

use function get_class;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\TypeResolver
 */
class TypeResolverTest extends TestCase
{
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
    public function testResolvingKeywords(string $keyword, string $expectedClass): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve($keyword, new Context(''));

        $this->assertInstanceOf($expectedClass, $resolvedType);
    }

    /**
     * @uses         \phpDocumentor\Reflection\Types\Context
     * @uses         \phpDocumentor\Reflection\Types\Object_
     * @uses         \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @dataProvider provideClassStrings
     */
    public function testResolvingClassStrings(string $classString, bool $throwsException): void
    {
        $fixture = new TypeResolver();

        if ($throwsException) {
            $this->expectException(RuntimeException::class);
        }

        $resolvedType = $fixture->resolve($classString, new Context(''));

        $this->assertInstanceOf(ClassString::class, $resolvedType);
    }

    /**
     * @uses         \phpDocumentor\Reflection\Types\Context
     * @uses         \phpDocumentor\Reflection\Types\Object_
     * @uses         \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @dataProvider provideInterfaceStrings
     */
    public function testResolvingInterfaceStrings(string $interfaceString, bool $throwsException): void
    {
        $fixture = new TypeResolver();

        if ($throwsException) {
            $this->expectException(RuntimeException::class);
        }

        $resolvedType = $fixture->resolve($interfaceString, new Context(''));

        $this->assertInstanceOf(InterfaceString::class, $resolvedType);
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
    public function testResolvingFQSENs(string $fqsen): void
    {
        $fixture = new TypeResolver();

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
    public function testResolvingRelativeQSENsBasedOnNamespace(): void
    {
        $fixture = new TypeResolver();

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
    public function testResolvingRelativeQSENsBasedOnNamespaceAlias(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve(
            'm\Array_',
            new Context('phpDocumentor\Reflection', ['m' => '\phpDocumentor\Reflection\Types'])
        );

        $this->assertInstanceOf(Object_::class, $resolvedType);
        $this->assertInstanceOf(Fqsen::class, $resolvedType->getFqsen());
        $this->assertSame('\phpDocumentor\Reflection\Types\Array_', (string) $resolvedType);
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
    public function testResolvingTypedArrays(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('string[]', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('string[]', (string) $resolvedType);
        $this->assertInstanceOf(Compound::class, $resolvedType->getKeyType());
        $this->assertInstanceOf(String_::class, $resolvedType->getValueType());
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
    public function testResolvingNullableTypes(): void
    {
        $fixture = new TypeResolver();

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
    public function testResolvingNestedTypedArrays(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('string[][]', new Context(''));

        $childValueType = $resolvedType->getValueType();

        $this->assertInstanceOf(Array_::class, $resolvedType);

        $this->assertSame('string[][]', (string) $resolvedType);
        $this->assertInstanceOf(Compound::class, $resolvedType->getKeyType());
        $this->assertInstanceOf(Array_::class, $childValueType);

        $this->assertSame('string[]', (string) $childValueType);
        $this->assertInstanceOf(Compound::class, $childValueType->getKeyType());
        $this->assertInstanceOf(String_::class, $childValueType->getValueType());
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
    public function testResolvingCompoundTypes(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('string|Reflection\DocBlock', new Context('phpDocumentor'));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('string|\phpDocumentor\Reflection\DocBlock', (string) $resolvedType);

        $firstType = $resolvedType->get(0);

        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf(String_::class, $firstType);
        $this->assertInstanceOf(Object_::class, $secondType);
        $this->assertInstanceOf(Fqsen::class, $secondType->getFqsen());
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
    public function testResolvingAmpersandCompoundTypes(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve(
            'Reflection\DocBlock&\PHPUnit\Framework\MockObject\MockObject ',
            new Context('phpDocumentor')
        );

        $this->assertInstanceOf(Intersection::class, $resolvedType);
        $this->assertSame(
            '\phpDocumentor\Reflection\DocBlock&\PHPUnit\Framework\MockObject\MockObject',
            (string) $resolvedType
        );

        $firstType = $resolvedType->get(0);

        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf(Object_::class, $firstType);
        $this->assertInstanceOf(Fqsen::class, $firstType->getFqsen());
        $this->assertInstanceOf(Object_::class, $secondType);
        $this->assertInstanceOf(Fqsen::class, $secondType->getFqsen());
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
    public function testResolvingMixedCompoundTypes(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve(
            '(Reflection\DocBlock&\PHPUnit\Framework\MockObject\MockObject)|null',
            new Context('phpDocumentor')
        );

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame(
            '(\phpDocumentor\Reflection\DocBlock&\PHPUnit\Framework\MockObject\MockObject)|null',
            (string) $resolvedType
        );

        $firstType = $resolvedType->get(0);

        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf(Expression::class, $firstType);
        $this->assertSame(
            '(\phpDocumentor\Reflection\DocBlock&\PHPUnit\Framework\MockObject\MockObject)',
            (string) $firstType
        );
        $this->assertInstanceOf(Null_::class, $secondType);

        $resolvedType = $firstType->getValueType();

        $firstSubType = $resolvedType->get(0);
        $secondSubType =  $resolvedType->get(1);

        $this->assertInstanceOf(Object_::class, $firstSubType);
        $this->assertInstanceOf(Fqsen::class, $secondSubType->getFqsen());
        $this->assertInstanceOf(Object_::class, $secondSubType);
        $this->assertInstanceOf(Fqsen::class, $secondSubType->getFqsen());
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
    public function testResolvingCompoundTypedArrayTypes(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('\stdClass[]|Reflection\DocBlock[]', new Context('phpDocumentor'));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('\stdClass[]|\phpDocumentor\Reflection\DocBlock[]', (string) $resolvedType);

        $firstType = $resolvedType->get(0);

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
    public function testResolvingNullableCompoundTypes(): void
    {
        $fixture = new TypeResolver();

        // Note that in PHP types it is illegal to use shorthand nullable
        // syntax with unions. This would be 'string|boolean|null' instead.
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
    public function testResolvingArrayExpressionObjectsTypes(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('(\stdClass|Reflection\DocBlock)[]', new Context('phpDocumentor'));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('(\stdClass|\phpDocumentor\Reflection\DocBlock)[]', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $this->assertInstanceOf(Compound::class, $valueType);

        $firstType = $valueType->get(0);

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
    public function testResolvingArrayExpressionSimpleTypes(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('(string|\stdClass|boolean)[]', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('(string|\stdClass|bool)[]', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $this->assertInstanceOf(Compound::class, $valueType);

        $firstType = $valueType->get(0);

        $secondType = $valueType->get(1);

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
    public function testResolvingArrayOfArrayExpressionTypes(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('(string|\stdClass)[][]', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $this->assertSame('(string|\stdClass)[][]', (string) $resolvedType);

        $parentArrayType = $resolvedType->getValueType();
        $this->assertInstanceOf(Array_::class, $parentArrayType);

        $valueType = $parentArrayType->getValueType();
        $this->assertInstanceOf(Compound::class, $valueType);

        $firstType = $valueType->get(0);

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
    public function testReturnEmptyCompoundOnAnUnclosedArrayExpressionType(): void
    {
        $fixture = new TypeResolver();

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
    public function testResolvingArrayExpressionOrCompoundTypes(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('\stdClass|(string|\stdClass)[]|bool', new Context(''));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('\stdClass|(string|\stdClass)[]|bool', (string) $resolvedType);

        $firstType = $resolvedType->get(0);
        $this->assertInstanceOf(Object_::class, $firstType);

        $secondType = $resolvedType->get(1);
        $this->assertInstanceOf(Array_::class, $secondType);

        $thirdType = $resolvedType->get(2);
        $this->assertInstanceOf(Boolean::class, $thirdType);

        $valueType = $secondType->getValueType();
        $this->assertInstanceOf(Compound::class, $valueType);

        $firstArrayType = $valueType->get(0);

        $secondArrayType = $valueType->get(1);

        $this->assertInstanceOf(String_::class, $firstArrayType);
        $this->assertInstanceOf(Object_::class, $secondArrayType);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Iterable_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     */
    public function testResolvingIterableExpressionSimpleTypes(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('iterable<string|\stdClass|boolean>', new Context(''));

        $this->assertInstanceOf(Iterable_::class, $resolvedType);
        $this->assertSame('iterable<string|\stdClass|bool>', (string) $resolvedType);

        $valueType = $resolvedType->getValueType();

        $this->assertInstanceOf(Compound::class, $valueType);

        $firstType = $valueType->get(0);

        $secondType = $valueType->get(1);

        $thirdType = $valueType->get(2);

        $this->assertInstanceOf(String_::class, $firstType);
        $this->assertInstanceOf(Object_::class, $secondType);
        $this->assertInstanceOf(Boolean::class, $thirdType);
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
    public function testResolvingCompoundTypesWithTwoArrays(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('integer[]|string[]', new Context(''));

        $this->assertInstanceOf(Compound::class, $resolvedType);
        $this->assertSame('int[]|string[]', (string) $resolvedType);

        $firstType = $resolvedType->get(0);

        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf(Array_::class, $firstType);
        $this->assertInstanceOf(Integer::class, $firstType->getValueType());
        $this->assertInstanceOf(Array_::class, $secondType);
        $this->assertInstanceOf(String_::class, $secondType->getValueType());
    }

    /**
     * @uses \phpDocumentor\Reflection\TypeResolver::resolve
     * @uses \phpDocumentor\Reflection\TypeResolver::<private>
     * @uses \phpDocumentor\Reflection\Types\Context
     *
     * @covers ::__construct
     * @covers ::addKeyword
     */
    public function testAddingAKeyword(): void
    {
        // Assign
        $typeMock = self::createStub(Type::class);

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
     */
    public function testAddingAKeywordFailsIfTypeClassDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $fixture = new TypeResolver();
        $fixture->addKeyword('mock', 'IDoNotExist');
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     *
     * @covers ::__construct
     * @covers ::addKeyword
     */
    public function testAddingAKeywordFailsIfTypeClassDoesNotImplementTypeInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $fixture = new TypeResolver();
        $fixture->addKeyword('mock', stdClass::class);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testExceptionIsThrownIfTypeIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $fixture = new TypeResolver();
        $fixture->resolve(' ', new Context(''));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testInvalidArrayOperator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $fixture = new TypeResolver();
        $fixture->resolve('[]', new Context(''));
    }

    /**
     * Returns a list of keywords and expected classes that are created from them.
     *
     * @return string[][]
     */
    public function provideKeywords(): array
    {
        return [
            ['string', String_::class],
            ['class-string', ClassString::class],
            ['html-escaped-string', HtmlEscapedString::class],
            ['lowercase-string', LowercaseString::class],
            ['non-empty-lowercase-string', NonEmptyLowercaseString::class],
            ['non-empty-string', NonEmptyString::class],
            ['numeric-string', NumericString::class],
            ['numeric', Numeric_::class],
            ['trait-string', TraitString::class],
            ['int', Integer::class],
            ['integer', Integer::class],
            ['positive-int', PositiveInteger::class],
            ['negative-int', NegativeInteger::class],
            ['float', Float_::class],
            ['double', Float_::class],
            ['bool', Boolean::class],
            ['boolean', Boolean::class],
            ['true', Boolean::class],
            ['true', True_::class],
            ['false', Boolean::class],
            ['false', False_::class],
            ['resource', Resource_::class],
            ['null', Null_::class],
            ['callable', Callable_::class],
            ['callable-string', CallableString::class],
            ['callback', Callable_::class],
            ['array', Array_::class],
            ['array-key', ArrayKey::class],
            ['scalar', Scalar::class],
            ['object', Object_::class],
            ['mixed', Mixed_::class],
            ['void', Void_::class],
            ['$this', This::class],
            ['static', Static_::class],
            ['self', Self_::class],
            ['parent', Parent_::class],
            ['iterable', Iterable_::class],
            ['never', Never_::class],
            ['literal-string', LiteralString::class],
            ['list', List_::class],
        ];
    }

    /**
     * Returns a list of class string types and whether they throw an exception.
     *
     * @return (string|bool)[][]
     */
    public function provideClassStrings(): array
    {
        return [
            ['class-string<\phpDocumentor\Reflection>', false],
            ['class-string<\phpDocumentor\Reflection\DocBlock>', false],
            ['class-string<string>', true],
        ];
    }

    /**
     * Returns a list of interface string types and whether they throw an exception.
     *
     * @return (string|bool)[][]
     */
    public function provideInterfaceStrings(): array
    {
        return [
            ['interface-string<\phpDocumentor\Reflection>', false],
            ['interface-string<\phpDocumentor\Reflection\DocBlock>', false],
            ['interface-string<string>', true],
        ];
    }

    /**
     * Provides a list of FQSENs to test the resolution patterns with.
     *
     * @return string[][]
     */
    public function provideFqcn(): array
    {
        return [
            'namespace' => ['\phpDocumentor\Reflection'],
            'class' => ['\phpDocumentor\Reflection\DocBlock'],
            'class with emoji' => ['\My😁Class'],
        ];
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testArrayKeyValueSpecification(): void
    {
        $fixture = new TypeResolver();
        $type = $fixture->resolve('array<string,array<int,string>>', new Context(''));

        $this->assertEquals(new Array_(new Array_(new String_(), new Integer()), new String_()), $type);
    }
}
