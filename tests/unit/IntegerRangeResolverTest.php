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

use phpDocumentor\Reflection\PseudoTypes\IntegerRange;
use phpDocumentor\Reflection\Types\Context;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers ::<private>
 * @coversDefaultClass \phpDocumentor\Reflection\TypeResolver
 */
class IntegerRangeResolverTest extends TestCase
{
    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::createType
     */
    public function testResolvingIntRange(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('int<-5, 5>', new Context(''));

        $this->assertInstanceOf(IntegerRange::class, $resolvedType);
        $this->assertSame('int<-5, 5>', (string) $resolvedType);

        $minValue = $resolvedType->getMinValue();
        $maxValue = $resolvedType->getMaxValue();

        $this->assertSame('-5', $minValue);
        $this->assertSame('5', $maxValue);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::createType
     */
    public function testResolvingIntRangeWithKeywords(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('int<min,max>', new Context(''));

        $this->assertInstanceOf(IntegerRange::class, $resolvedType);
        $this->assertSame('int<min, max>', (string) $resolvedType);

        $minValue = $resolvedType->getMinValue();
        $maxValue = $resolvedType->getMaxValue();

        $this->assertSame('min', $minValue);
        $this->assertSame('max', $maxValue);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::createType
     */
    public function testResolvingIntRangeErrorMissingMaxValue(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('int<min,max> has not the correct format');

        $fixture = new TypeResolver();
        $resolvedType = $fixture->resolve('int<min,>', new Context(''));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::createType
     */
    public function testResolvingIntRangeErrorMisingMinValue(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unexpected token ",", expected type at offset 4');

        $fixture = new TypeResolver();
        $resolvedType = $fixture->resolve('int<,max>', new Context(''));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::createType
     */
    public function testResolvingIntRangeErrorMisingComma(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('int<min,max> has not the correct format');

        $fixture = new TypeResolver();
        $resolvedType = $fixture->resolve('int<min|max>', new Context(''));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\Collection
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     * @covers ::__construct
     * @covers ::resolve
     * @covers ::createType
     */
    public function testResolvingIntRangeErrorMissingEnd(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unexpected token "", expected \'>\' at offset 11');

        $fixture = new TypeResolver();
        $resolvedType = $fixture->resolve('int<min,max', new Context(''));
    }
}
