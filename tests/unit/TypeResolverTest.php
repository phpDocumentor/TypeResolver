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
use phpDocumentor\Reflection\Types\Object_;

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
     * @uses phpDocumentor\Reflection\Types\Context
     * @uses phpDocumentor\Reflection\Types\Array_
     * @uses phpDocumentor\Reflection\Types\Object_
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
     * @uses phpDocumentor\Reflection\Types\Context
     * @uses phpDocumentor\Reflection\Types\Object_
     * @uses phpDocumentor\Reflection\Fqsen
     * @uses phpDocumentor\Reflection\FqsenResolver
     *
     * @dataProvider provideFqcn
     */
    public function testResolvingFQSENs($fqsen)
    {
        $fixture = new TypeResolver();

        /** @var Object_ $resolvedType */
        $resolvedType = $fixture->resolve($fqsen, new Context(''));

        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Object_', $resolvedType);
        $this->assertInstanceOf('phpDocumentor\Reflection\Fqsen', $resolvedType->getFqsen());
        $this->assertSame($fqsen, (string)$resolvedType);
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses phpDocumentor\Reflection\Types\Context
     * @uses phpDocumentor\Reflection\Types\Object_
     * @uses phpDocumentor\Reflection\Fqsen
     * @uses phpDocumentor\Reflection\FqsenResolver
     */
    public function testResolvingRelativeQSENsBasedOnNamespace()
    {
        $fixture = new TypeResolver();

        /** @var Object_ $resolvedType */
        $resolvedType = $fixture->resolve('DocBlock', new Context('phpDocumentor\Reflection'));

        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Object_', $resolvedType);
        $this->assertInstanceOf('phpDocumentor\Reflection\Fqsen', $resolvedType->getFqsen());
        $this->assertSame('\phpDocumentor\Reflection\DocBlock', (string)$resolvedType);
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses phpDocumentor\Reflection\Types\Context
     * @uses phpDocumentor\Reflection\Types\Object_
     * @uses phpDocumentor\Reflection\Fqsen
     * @uses phpDocumentor\Reflection\FqsenResolver
     */
    public function testResolvingRelativeQSENsBasedOnNamespaceAlias()
    {
        $fixture = new TypeResolver();

        /** @var Object_ $resolvedType */
        $resolvedType = $fixture->resolve(
            'm\MockInterface',
            new Context('phpDocumentor\Reflection', ['m' => '\Mockery'])
        );

        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Object_', $resolvedType);
        $this->assertInstanceOf('phpDocumentor\Reflection\Fqsen', $resolvedType->getFqsen());
        $this->assertSame('\Mockery\MockInterface', (string)$resolvedType);
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
    public function testResolvingTypedArrays()
    {
        $fixture = new TypeResolver();

        /** @var Array_ $resolvedType */
        $resolvedType = $fixture->resolve('string[]', new Context(''));

        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Array_', $resolvedType);
        $this->assertSame('string[]', (string)$resolvedType);
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Compound', $resolvedType->getKeyType());
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\String_', $resolvedType->getValueType());
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

        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Array_', $resolvedType);

        $this->assertSame('string[][]', (string)$resolvedType);
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Compound', $resolvedType->getKeyType());
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Array_', $childValueType);

        $this->assertSame('string[]', (string)$childValueType);
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Compound', $childValueType->getKeyType());
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\String_', $childValueType->getValueType());
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses phpDocumentor\Reflection\Types\Context
     * @uses phpDocumentor\Reflection\Types\Compound
     * @uses phpDocumentor\Reflection\Types\String_
     * @uses phpDocumentor\Reflection\Types\Object_
     * @uses phpDocumentor\Reflection\Fqsen
     * @uses phpDocumentor\Reflection\FqsenResolver
     */
    public function testResolvingCompoundTypes()
    {
        $fixture = new TypeResolver();

        /** @var Compound $resolvedType */
        $resolvedType = $fixture->resolve('string|Reflection\DocBlock', new Context('phpDocumentor'));

        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Compound', $resolvedType);
        $this->assertSame('string|\phpDocumentor\Reflection\DocBlock', (string)$resolvedType);

        /** @var String $secondType */
        $firstType = $resolvedType->get(0);

        /** @var Object_ $secondType */
        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf('phpDocumentor\Reflection\Types\String_', $firstType);
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Object_', $secondType);
        $this->assertInstanceOf('phpDocumentor\Reflection\Fqsen', $secondType->getFqsen());
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::<private>
     *
     * @uses phpDocumentor\Reflection\Types\Context
     * @uses phpDocumentor\Reflection\Types\Compound
     * @uses phpDocumentor\Reflection\Types\Array_
     * @uses phpDocumentor\Reflection\Types\Object_
     * @uses phpDocumentor\Reflection\Fqsen
     * @uses phpDocumentor\Reflection\FqsenResolver
     */
    public function testResolvingCompoundTypedArrayTypes()
    {
        $fixture = new TypeResolver();

        /** @var Compound $resolvedType */
        $resolvedType = $fixture->resolve('\stdClass[]|Reflection\DocBlock[]', new Context('phpDocumentor'));

        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Compound', $resolvedType);
        $this->assertSame('\stdClass[]|\phpDocumentor\Reflection\DocBlock[]', (string)$resolvedType);

        /** @var Array_ $secondType */
        $firstType = $resolvedType->get(0);

        /** @var Array_ $secondType */
        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Array_', $firstType);
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Array_', $secondType);
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Object_', $firstType->getValueType());
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Object_', $secondType->getValueType());
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
     * @uses phpDocumentor\Reflection\Types\Context
     * @uses phpDocumentor\Reflection\Types\Compound
     * @uses phpDocumentor\Reflection\Types\Array_
     * @uses phpDocumentor\Reflection\Types\Integer
     * @uses phpDocumentor\Reflection\Types\String_
     */
    public function testResolvingCompoundTypesWithTwoArrays()
    {
        $fixture = new TypeResolver();

        /** @var Compound $resolvedType */
        $resolvedType = $fixture->resolve('integer[]|string[]', new Context(''));

        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Compound', $resolvedType);
        $this->assertSame('int[]|string[]', (string)$resolvedType);

        /** @var Array_ $firstType */
        $firstType = $resolvedType->get(0);

        /** @var Array_ $secondType */
        $secondType = $resolvedType->get(1);

        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Array_', $firstType);
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Integer', $firstType->getValueType());
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\Array_', $secondType);
        $this->assertInstanceOf('phpDocumentor\Reflection\Types\String_', $secondType->getValueType());
    }

    /**
     * @covers ::__construct
     * @covers ::addKeyword
     * @uses phpDocumentor\Reflection\TypeResolver::resolve
     * @uses phpDocumentor\Reflection\TypeResolver::<private>
     * @uses phpDocumentor\Reflection\Types\Context
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
     * @uses phpDocumentor\Reflection\Types\Context
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
     * @uses phpDocumentor\Reflection\Types\Context
     * @expectedException \InvalidArgumentException
     */
    public function testAddingAKeywordFailsIfTypeClassDoesNotImplementTypeInterface()
    {
        $fixture = new TypeResolver();
        $fixture->addKeyword('mock', 'stdClass');
    }

    /**
     * @covers ::__construct
     * @covers ::resolve
     * @uses phpDocumentor\Reflection\Types\Context
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
     * @uses phpDocumentor\Reflection\Types\Context
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
            ['string', 'phpDocumentor\Reflection\Types\String_'],
            ['int', 'phpDocumentor\Reflection\Types\Integer'],
            ['integer', 'phpDocumentor\Reflection\Types\Integer'],
            ['float', 'phpDocumentor\Reflection\Types\Float_'],
            ['double', 'phpDocumentor\Reflection\Types\Float_'],
            ['bool', 'phpDocumentor\Reflection\Types\Boolean'],
            ['boolean', 'phpDocumentor\Reflection\Types\Boolean'],
            ['resource', 'phpDocumentor\Reflection\Types\Resource'],
            ['null', 'phpDocumentor\Reflection\Types\Null_'],
            ['callable', 'phpDocumentor\Reflection\Types\Callable_'],
            ['callback', 'phpDocumentor\Reflection\Types\Callable_'],
            ['array', 'phpDocumentor\Reflection\Types\Array_'],
            ['scalar', 'phpDocumentor\Reflection\Types\Scalar'],
            ['object', 'phpDocumentor\Reflection\Types\Object_'],
            ['mixed', 'phpDocumentor\Reflection\Types\Mixed'],
            ['void', 'phpDocumentor\Reflection\Types\Void_'],
            ['$this', 'phpDocumentor\Reflection\Types\This'],
            ['static', 'phpDocumentor\Reflection\Types\Static_'],
            ['self', 'phpDocumentor\Reflection\Types\Self_'],
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
