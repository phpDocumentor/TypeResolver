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

use phpDocumentor\Reflection\PseudoTypes\Numeric_;
use phpDocumentor\Reflection\PseudoTypes\NumericString;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

class NumericResolverTest extends TestCase
{
    /**
     * @uses \phpDocumentor\Reflection\Types\Context
     * @uses \phpDocumentor\Reflection\Types\Compound
     * @uses \phpDocumentor\Reflection\Types\String_
     *
     */
    public function testResolvingIntRange(): void
    {
        $fixture = new TypeResolver();

        $resolvedType = $fixture->resolve('numeric', new Context(''));

        $this->assertInstanceOf(Numeric_::class, $resolvedType);
        $this->assertSame('numeric', (string) $resolvedType);

        $underlyingType = $resolvedType->underlyingType();
        $this->assertInstanceOf(Compound::class, $underlyingType);
        $this->assertFalse($underlyingType->contains(new String_()));
        $this->assertTrue($underlyingType->contains(new NumericString()));
    }
}
