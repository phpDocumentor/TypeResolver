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

use Doctrine\Deprecations\PHPUnit\VerifyDeprecations;
use InvalidArgumentException;
use phpDocumentor\Reflection\PseudoTypes\ArrayKey;
use phpDocumentor\Reflection\PseudoTypes\ArrayShape;
use phpDocumentor\Reflection\PseudoTypes\ArrayShapeItem;
use phpDocumentor\Reflection\PseudoTypes\CallableArray;
use phpDocumentor\Reflection\PseudoTypes\CallableString;
use phpDocumentor\Reflection\PseudoTypes\ClassString;
use phpDocumentor\Reflection\PseudoTypes\Conditional;
use phpDocumentor\Reflection\PseudoTypes\ConditionalForParameter;
use phpDocumentor\Reflection\PseudoTypes\ConstExpression;
use phpDocumentor\Reflection\PseudoTypes\EnumString;
use phpDocumentor\Reflection\PseudoTypes\False_;
use phpDocumentor\Reflection\PseudoTypes\FloatValue;
use phpDocumentor\Reflection\PseudoTypes\Generic;
use phpDocumentor\Reflection\PseudoTypes\HtmlEscapedString;
use phpDocumentor\Reflection\PseudoTypes\IntegerRange;
use phpDocumentor\Reflection\PseudoTypes\IntegerValue;
use phpDocumentor\Reflection\PseudoTypes\InterfaceString;
use phpDocumentor\Reflection\PseudoTypes\IntMask;
use phpDocumentor\Reflection\PseudoTypes\IntMaskOf;
use phpDocumentor\Reflection\PseudoTypes\KeyOf;
use phpDocumentor\Reflection\PseudoTypes\List_;
use phpDocumentor\Reflection\PseudoTypes\ListShape;
use phpDocumentor\Reflection\PseudoTypes\ListShapeItem;
use phpDocumentor\Reflection\PseudoTypes\LiteralString;
use phpDocumentor\Reflection\PseudoTypes\LowercaseString;
use phpDocumentor\Reflection\PseudoTypes\NegativeInteger;
use phpDocumentor\Reflection\PseudoTypes\NeverReturn;
use phpDocumentor\Reflection\PseudoTypes\NeverReturns;
use phpDocumentor\Reflection\PseudoTypes\NonEmptyArray;
use phpDocumentor\Reflection\PseudoTypes\NonEmptyList;
use phpDocumentor\Reflection\PseudoTypes\NonEmptyLowercaseString;
use phpDocumentor\Reflection\PseudoTypes\NonEmptyString;
use phpDocumentor\Reflection\PseudoTypes\NonFalsyString;
use phpDocumentor\Reflection\PseudoTypes\NonNegativeInteger;
use phpDocumentor\Reflection\PseudoTypes\NonPositiveInteger;
use phpDocumentor\Reflection\PseudoTypes\NonZeroInteger;
use phpDocumentor\Reflection\PseudoTypes\NoReturn;
use phpDocumentor\Reflection\PseudoTypes\Numeric_;
use phpDocumentor\Reflection\PseudoTypes\NumericString;
use phpDocumentor\Reflection\PseudoTypes\ObjectShape;
use phpDocumentor\Reflection\PseudoTypes\ObjectShapeItem;
use phpDocumentor\Reflection\PseudoTypes\OffsetAccess;
use phpDocumentor\Reflection\PseudoTypes\PositiveInteger;
use phpDocumentor\Reflection\PseudoTypes\PrivatePropertiesOf;
use phpDocumentor\Reflection\PseudoTypes\PropertiesOf;
use phpDocumentor\Reflection\PseudoTypes\ProtectedPropertiesOf;
use phpDocumentor\Reflection\PseudoTypes\PublicPropertiesOf;
use phpDocumentor\Reflection\PseudoTypes\Scalar;
use phpDocumentor\Reflection\PseudoTypes\StringValue;
use phpDocumentor\Reflection\PseudoTypes\TraitString;
use phpDocumentor\Reflection\PseudoTypes\True_;
use phpDocumentor\Reflection\PseudoTypes\TruthyString;
use phpDocumentor\Reflection\PseudoTypes\ValueOf;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\CallableParameter;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Expression;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Intersection;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Never_;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Parent_;
use phpDocumentor\Reflection\Types\Resource_;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\Static_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\This;
use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

use function get_class;

class TypeResolverTest extends TestCase
{
    use VerifyDeprecations;

    /**
     * @uses         \phpDocumentor\Reflection\Types\Context
     * @uses         \phpDocumentor\Reflection\Types\Array_
     * @uses         \phpDocumentor\Reflection\Types\Object_
     *
     * @param class-string $expectedClass
     *
     * @dataProvider provideKeywords
     */
    public function testResolvingKeywords(string $keyword, string $expectedClass): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve($keyword, new Context(''));
        $this->assertInstanceOf($expectedClass, $resolvedType);

        $resolvedType = $fixture->resolve($keyword);
        $this->assertInstanceOf($expectedClass, $resolvedType);
    }

    /**
     * @uses         \phpDocumentor\Reflection\Types\Context
     * @uses         \phpDocumentor\Reflection\Types\Object_
     * @uses         \phpDocumentor\Reflection\Fqsen
     * @uses         \phpDocumentor\Reflection\FqsenResolver
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
     */
    public function testResolvingNestedTypedArrays(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('string[][]', new Context(''));

        $this->assertInstanceOf(Array_::class, $resolvedType);
        $childValueType = $resolvedType->getValueType();

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

        $this->assertInstanceOf(Intersection::class, $resolvedType);
        $firstSubType = $resolvedType->get(0);
        $secondSubType =  $resolvedType->get(1);

        $this->assertInstanceOf(Object_::class, $firstSubType);
        $this->assertInstanceOf(Fqsen::class, $firstSubType->getFqsen());
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
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
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
     */
    public function testReturnEmptyCompoundOnAnUnclosedArrayExpressionType(): void
    {
        $this->expectException(RuntimeException::class);
        $fixture = new TypeResolver();
        $fixture->resolve('(string|\stdClass', new Context(''));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Array_
     * @uses \phpDocumentor\Reflection\Types\Object_
     * @uses \phpDocumentor\Reflection\Fqsen
     * @uses \phpDocumentor\Reflection\FqsenResolver
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
     */
    public function testAddingAKeywordFailsIfTypeClassDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $fixture = new TypeResolver();
        $fixture->addKeyword('mock', 'IDoNotExist');
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     */
    public function testAddingAKeywordFailsIfTypeClassDoesNotImplementTypeInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $fixture = new TypeResolver();
        $fixture->addKeyword('mock', stdClass::class);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     */
    public function testExceptionIsThrownIfTypeIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $fixture = new TypeResolver();
        $fixture->resolve(' ', new Context(''));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     */
    public function testInvalidArrayOperator(): void
    {
        $this->expectException(RuntimeException::class);
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
            ['callable-array', CallableArray::class],
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
            ['never-return', NeverReturn::class],
            ['never-returns', NeverReturns::class],
            ['no-return', NoReturn::class],
            ['literal-string', LiteralString::class],
            ['list', List_::class],
            ['non-empty-list', NonEmptyList::class],
            ['non-empty-array', NonEmptyArray::class],
            ['non-falsy-string', NonFalsyString::class],
            ['truthy-string', TruthyString::class],
            ['non-positive-int', NonPositiveInteger::class],
            ['non-negative-int', NonNegativeInteger::class],
            ['non-zero-int', NonZeroInteger::class],
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
     */
    public function testArrayKeyValueSpecification(): void
    {
        $fixture = new TypeResolver();
        $type = $fixture->resolve('array<string,array<int,string>>', new Context(''));

        $this->assertEquals(new Array_(new Array_(new String_(), new Integer()), new String_()), $type);
    }

    /**
     * @dataProvider typeProvider
     * @dataProvider genericsProvider
     * @dataProvider callableProvider
     * @dataProvider constExpressions
     * @dataProvider shapeStructures
     * @testdox create type from $type
     */
    public function testTypeBuilding(string $type, Type $expected, bool $deprecation = false): void
    {
        $this->expectNoDeprecationWithIdentifier('https://github.com/phpDocumentor/TypeResolver/issues/184');

        $fixture = new TypeResolver();
        $actual = $fixture->resolve($type, new Context('phpDocumentor'));

        self::assertEquals($expected, $actual);
    }

    /**
     * @dataProvider illegalLegacyFormatProvider
     * @testdox create type from $type
     */
    public function testTypeBuildingThrowsError(string $type, Type $expected): void
    {
        $this->expectDeprecationWithIdentifier('https://github.com/phpDocumentor/TypeResolver/issues/184');

        $fixture = new TypeResolver();
        $actual = $fixture->resolve($type, new Context('phpDocumentor'));

        self::assertEquals($expected, $actual);
    }

    /**
     * @return array<array{0: string, 1: Type}>
     */
    public function typeProvider(): array
    {
        return [
            [
                'string',
                new String_(),
            ],
            [
                '( string )',
                new String_(),
            ],
            [
                '\\Foo\Bar\\Baz',
                new Object_(new Fqsen('\\Foo\Bar\\Baz')),
            ],
            [
                'string|int',
                new Compound(
                    [
                        new String_(),
                        new Integer(),
                    ]
                ),
            ],
            [
                'string&int',
                new Intersection(
                    [
                        new String_(),
                        new Integer(),
                    ]
                ),
            ],
            [
                'string & (int | float)',
                new Intersection(
                    [
                        new String_(),
                        new Expression(
                            new Compound(
                                [
                                    new Integer(),
                                    new Float_(),
                                ]
                            )
                        ),
                    ]
                ),
            ],
            [
                '(A&B)|C|(D&E)',
                new Compound([
                    new Expression(
                        new Intersection([
                            new Object_(new Fqsen('\\phpDocumentor\\A')),
                            new Object_(new Fqsen('\\phpDocumentor\\B')),
                        ]),
                    ),
                    new Object_(new Fqsen('\\phpDocumentor\\C')),
                    new Expression(
                        new Intersection([
                            new Object_(new Fqsen('\\phpDocumentor\\D')),
                            new Object_(new Fqsen('\\phpDocumentor\\E')),
                        ]),
                    ),
                ]),
            ],
            [
                'array',
                new Array_(),
            ],
            [
                'string[]',
                new Array_(
                    new String_()
                ),
            ],
            [
                'mixed[]',
                new Array_(
                    new Mixed_()
                ),
            ],
            [
                '$this',
                new This(),
            ],
            [
                '?int',
                new Nullable(
                    new Integer()
                ),
            ],
            [
                'static',
                new Static_(),
            ],
            [
                'self',
                new Self_(),
            ],
            [
                '($size is positive-int ? non-empty-array : array)',
                new ConditionalForParameter(
                    false,
                    'size',
                    new PositiveInteger(),
                    new NonEmptyArray(),
                    new Array_()
                ),
            ],
            [
                '($size is not positive-int ? non-empty-array : int)',
                new ConditionalForParameter(
                    true,
                    'size',
                    new PositiveInteger(),
                    new NonEmptyArray(),
                    new Integer()
                ),
            ],
            [
                '(T is int ? static : array<static>)',
                new Conditional(
                    false,
                    new Object_(new Fqsen('\\phpDocumentor\\T')),
                    new Integer(),
                    new Static_(),
                    new Array_(new Static_())
                ),
            ],
            [
                '(T is not int ? self : array<static>)',
                new Conditional(
                    true,
                    new Object_(new Fqsen('\\phpDocumentor\\T')),
                    new Integer(),
                    new Self_(),
                    new Array_(new Static_())
                ),
            ],
            [
                "MyArray['bar']",
                new OffsetAccess(
                    new Object_(new Fqsen('\\phpDocumentor\\MyArray')),
                    new StringValue('bar')
                ),
            ],
            [
                '100.5',
                new FloatValue(100.5),
            ],
        ];
    }

    /**
     * @return array<array{0: string, 1: Type}>
     */
    public function genericsProvider(): array
    {
        return [
            [
                'array<int, Foo\\Bar>',
                new Array_(
                    new Object_(new Fqsen('\\phpDocumentor\\Foo\\Bar')),
                    new Integer()
                ),
            ],
            [
                'array<key-of<Foo\\Bar::SOME_CONSTANT>, string>',
                new Array_(
                    new String_(),
                    new KeyOf(new ConstExpression(
                        new Object_(new Fqsen('\\phpDocumentor\\Foo\\Bar')),
                        'SOME_CONSTANT'
                    ))
                ),
            ],
            [
                'array<value-of<Foo\\Bar::SOME_CONSTANT>, string>',
                new Array_(
                    new String_(),
                    new ValueOf(new ConstExpression(
                        new Object_(new Fqsen('\\phpDocumentor\\Foo\\Bar')),
                        'SOME_CONSTANT'
                    ))
                ),
            ],
            [
                'array<Foo\\Bar::*, string>',
                new Array_(
                    new String_(),
                    new ConstExpression(new Object_(new Fqsen('\\phpDocumentor\\Foo\\Bar')), '*')
                ),
            ],
            [
                'array<self::SOME_CONSTANT_FIRST|self::SOME_CONSTANT_SECOND, string>',
                new Array_(
                    new String_(),
                    new Compound([
                        new ConstExpression(new Self_(), 'SOME_CONSTANT_FIRST'),
                        new ConstExpression(new Self_(), 'SOME_CONSTANT_SECOND'),
                    ])
                ),
            ],
            [
                'array<string|int, Foo\\Bar>',
                new Array_(
                    new Object_(new Fqsen('\\phpDocumentor\\Foo\\Bar')),
                    new Compound(
                        [
                            new String_(),
                            new Integer(),
                        ]
                    )
                ),
            ],
            [
                'non-empty-array<string|int>',
                new NonEmptyArray(
                    new Compound(
                        [
                            new String_(),
                            new Integer(),
                        ]
                    )
                ),
            ],
            [
                'non-empty-array<string|int, Foo\\Bar>',
                new NonEmptyArray(
                    new Object_(new Fqsen('\\phpDocumentor\\Foo\\Bar')),
                    new Compound(
                        [
                            new String_(),
                            new Integer(),
                        ]
                    )
                ),
            ],
            [
                'Collection<array-key, int>[]',
                new Array_(
                    new Generic(
                        new Fqsen('\\phpDocumentor\\Collection'),
                        [
                            new ArrayKey(),
                            new Integer(),
                        ]
                    )
                ),
            ],
            [
                'class-string',
                new ClassString(),
            ],
            [
                'class-string<Foo>',
                new ClassString(new Object_(new Fqsen('\\phpDocumentor\\Foo'))),
            ],
            [
                'class-string<Foo|Bar>',
                new ClassString(
                    new Compound([
                        new Object_(new Fqsen('\\phpDocumentor\\Foo')),
                        new Object_(new Fqsen('\\phpDocumentor\\Bar')),
                    ])
                ),
            ],
            [
                'interface-string',
                new InterfaceString(),
            ],
            [
                'interface-string<Foo>',
                new InterfaceString(new Object_(new Fqsen('\\phpDocumentor\\Foo'))),
            ],
            [
                'interface-string<Foo|Bar>',
                new InterfaceString(
                    new Compound([
                        new Object_(new Fqsen('\\phpDocumentor\\Foo')),
                        new Object_(new Fqsen('\\phpDocumentor\\Bar')),
                    ])
                ),
            ],
            [
                'trait-string',
                new TraitString(),
            ],
            [
                'trait-string<Foo>',
                new TraitString(new Object_(new Fqsen('\\phpDocumentor\\Foo'))),
            ],
            [
                'trait-string<Foo|Bar>',
                new TraitString(
                    new Compound([
                        new Object_(new Fqsen('\\phpDocumentor\\Foo')),
                        new Object_(new Fqsen('\\phpDocumentor\\Bar')),
                    ])
                ),
            ],
            [
                'enum-string',
                new EnumString(),
            ],
            [
                'enum-string<MyEnum>',
                new EnumString(new Object_(new Fqsen('\\phpDocumentor\\MyEnum'))),
            ],
            [
                'enum-string<MyEnumFirst|MyEnumSecond>',
                new EnumString(
                    new Compound([
                        new Object_(new Fqsen('\\phpDocumentor\\MyEnumFirst')),
                        new Object_(new Fqsen('\\phpDocumentor\\MyEnumSecond')),
                    ])
                ),
            ],
            [
                'List<Foo>',
                new List_(new Object_(new Fqsen('\\phpDocumentor\\Foo'))),
            ],
            [
                'non-empty-list<Foo>',
                new NonEmptyList(new Object_(new Fqsen('\\phpDocumentor\\Foo'))),
            ],
            [
                'int<1, 100>',
                new IntegerRange('1', '100'),
            ],
            [
                'key-of<Type::ARRAY_CONST>',
                new KeyOf(new ConstExpression(new Object_(new Fqsen('\\phpDocumentor\\Type')), 'ARRAY_CONST')),
            ],
            [
                'value-of<Type::ARRAY_CONST>',
                new ValueOf(new ConstExpression(new Object_(new Fqsen('\\phpDocumentor\\Type')), 'ARRAY_CONST')),
            ],
            [
                'int-mask<1, 2, 4>',
                new IntMask(new IntegerValue(1), new IntegerValue(2), new IntegerValue(4)),
            ],
            [
                'int-mask-of<1|2|4>',
                new IntMaskOf(new Compound([new IntegerValue(1), new IntegerValue(2), new IntegerValue(4)])),
            ],
            [
                'int-mask-of<Foo::INT_*>',
                new IntMaskOf(new ConstExpression(new Object_(new Fqsen('\\phpDocumentor\\Foo')), 'INT_*')),
            ],
            [
                'iterable<int, string>',
                new Iterable_(new String_(), new Integer()),
            ],
            [
                'static<FirstClass, SecondClass, ThirdClass>',
                new Static_(
                    new Object_(new Fqsen('\\phpDocumentor\\FirstClass')),
                    new Object_(new Fqsen('\\phpDocumentor\\SecondClass')),
                    new Object_(new Fqsen('\\phpDocumentor\\ThirdClass')),
                ),
            ],
            [
                'self<FirstClass, SecondClass, ThirdClass>',
                new Self_(
                    new Object_(new Fqsen('\\phpDocumentor\\FirstClass')),
                    new Object_(new Fqsen('\\phpDocumentor\\SecondClass')),
                    new Object_(new Fqsen('\\phpDocumentor\\ThirdClass')),
                ),
            ],
            [
                'array<mixed>',
                new Array_(new Mixed_()),
            ],
            [
                'iterable<mixed>',
                new Iterable_(new Mixed_()),
            ],
            [
                'non-empty-array<mixed>',
                new NonEmptyArray(new Mixed_()),
            ],
            [
                'non-empty-list<mixed>',
                new NonEmptyList(new Mixed_()),
            ],
            [
                'properties-of<self>',
                new PropertiesOf(new Self_()),
            ],
            [
                'public-properties-of<self>',
                new PublicPropertiesOf(new Self_()),
            ],
            [
                'protected-properties-of<self>',
                new ProtectedPropertiesOf(new Self_()),
            ],
            [
                'private-properties-of<self>',
                new PrivatePropertiesOf(new Self_()),
            ],
        ];
    }

    /**
     * @return array<array{0: string, 1: Type}>
     */
    public function callableProvider(): array
    {
        return [
            [
                'callable',
                new Callable_(),
            ],
            [
                'pure-callable(int): int',
                new Callable_(
                    'pure-callable',
                    [new CallableParameter(new Integer())],
                    new Integer()
                ),
            ],
            [
                'callable()',
                new Callable_(),
            ],
            [
                'callable(): Foo',
                new Callable_('callable', [], new Object_(new Fqsen('\\phpDocumentor\\Foo'))),
            ],
            [
                'Closure(): Foo',
                new Callable_('Closure', [], new Object_(new Fqsen('\\phpDocumentor\\Foo'))),
            ],
            [
                '\Closure(): Foo',
                new Callable_('\Closure', [], new Object_(new Fqsen('\\phpDocumentor\\Foo'))),
            ],
            [
                'callable(): (Foo&Bar)',
                new Callable_(
                    'callable',
                    [],
                    new Intersection(
                        [
                            new Object_(new Fqsen('\\phpDocumentor\\Foo')),
                            new Object_(new Fqsen('\\phpDocumentor\\Bar')),
                        ]
                    )
                ),
            ],
            [
                'callable(A&...$a=, B&...=, C): Foo',
                new Callable_(
                    'callable',
                    [
                        new CallableParameter(
                            new Object_(new Fqsen('\\phpDocumentor\\A')),
                            'a',
                            true,
                            true,
                            true
                        ),
                        new CallableParameter(
                            new Object_(new Fqsen('\\phpDocumentor\\B')),
                            null,
                            true,
                            true,
                            true
                        ),
                        new CallableParameter(
                            new Object_(new Fqsen('\\phpDocumentor\\C')),
                            null,
                            false,
                            false,
                            false
                        ),
                    ],
                    new Object_(new Fqsen('\\phpDocumentor\\Foo'))
                ),
            ],
            [
                'Closure(mixed): (callable(mixed): mixed)',
                new Callable_(
                    'Closure',
                    [
                        new CallableParameter(
                            new Mixed_(),
                            null,
                            false,
                            false,
                            false
                        ),
                    ],
                    new Callable_(
                        'callable',
                        [
                            new CallableParameter(
                                new Mixed_(),
                                null,
                                false,
                                false,
                                false
                            ),
                        ],
                        new Mixed_()
                    )
                ),
            ],
        ];
    }

    /**
     * @return array<array{0: string, 1: Type}>
     */
    public function constExpressions(): array
    {
        return [
            [
                '123',
                new IntegerValue(123),
            ],
            [
                'true',
                new True_(),
            ],
            [
                '123.2',
                new FloatValue(123.2),
            ],
            [
                '"bar"',
                new StringValue('bar'),
            ],
            [
                'Foo::FOO_CONSTANT',
                new ConstExpression(new Object_(new Fqsen('\\phpDocumentor\\Foo')), 'FOO_CONSTANT'),
            ],
            [
                'Foo::FOO_*',
                new ConstExpression(new Object_(new Fqsen('\\phpDocumentor\\Foo')), 'FOO_*'),
            ],
            [
                'self::*|null',
                new Compound([new ConstExpression(new Self_(), '*'), new Null_()]),
            ],
        ];
    }

    /**
     * @return array<array{0: string, 1: Type}>
     */
    public function shapeStructures(): array
    {
        return [
            [
                'array{foo: string, bar: int}',
                new ArrayShape(
                    new ArrayShapeItem('foo', new String_(), false),
                    new ArrayShapeItem('bar', new Integer(), false)
                ),
            ],
            [
                'array{string, int}',
                new ArrayShape(
                    new ArrayShapeItem(null, new String_(), false),
                    new ArrayShapeItem(null, new Integer(), false)
                ),
            ],
            [
                'array{foo?: string, bar: int}',
                new ArrayShape(
                    new ArrayShapeItem('foo', new String_(), true),
                    new ArrayShapeItem('bar', new Integer(), false)
                ),
            ],
            [
                'object{foo: string, bar: int}',
                new ObjectShape(
                    new ObjectShapeItem('foo', new String_(), false),
                    new ObjectShapeItem('bar', new Integer(), false)
                ),
            ],
            [
                'list{1}',
                new ListShape(
                    new ListShapeItem(null, new IntegerValue(1), false)
                ),
            ],
        ];
    }

    /**
     * @return array<array{0: string, 1: Type}>
     */
    public function illegalLegacyFormatProvider(): array
    {
        return [
            [
                '?string |bool',
                new Nullable(new String_()),
            ],
            [
                '?string|?bool',
                new Nullable(new String_()),
            ],
            [
                '?string|?bool|null',
                new Nullable(new String_()),
            ],
            [
                '?string|bool|Foo',
                new Nullable(new String_()),
            ],
            [
                '?string&bool',
                new Nullable(new String_()),
            ],
            [
                '?string&bool|Foo',
                new Nullable(new String_()),
            ],
            [
                '?string&?bool|null',
                new Nullable(new String_()),
            ],
        ];
    }

    public function testCreateTypeFromNull(): void
    {
        $fixture = new TypeResolver();

        $this->assertEquals(new Mixed_(), $fixture->createType(null, new Context('')));
    }
}
