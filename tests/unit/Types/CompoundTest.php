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

namespace phpDocumentor\Reflection\Types;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Compound
 */
class CompoundTest extends TestCase
{
    /**
     * @covers ::__construct
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A compound type can only have other types as elements
     */
    public function testCompoundCannotBeConstructedFromType() : void
    {
        new Compound(['foo']);
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Compound::__construct
     * @uses \phpDocumentor\Reflection\Types\Compound::has
     * @uses \phpDocumentor\Reflection\Types\Integer
     *
     * @covers ::get
     */
    public function testCompoundGetType() : void
    {
        $integer = new Integer();

        $this->assertSame($integer, (new Compound([$integer]))->get(0));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Compound::__construct
     * @uses \phpDocumentor\Reflection\Types\Compound::has
     *
     * @covers ::get
     */
    public function testCompoundGetNotExistingType() : void
    {
        $this->assertNull((new Compound([]))->get(0));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Compound::__construct
     * @uses \phpDocumentor\Reflection\Types\Integer
     *
     * @covers ::has
     */
    public function testCompoundHasType() : void
    {
        $this->assertTrue((new Compound([new Integer()]))->has(0));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Compound::__construct
     *
     * @covers ::has
     */
    public function testCompoundHasNotExistingType() : void
    {
        $this->assertFalse((new Compound([]))->has(0));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Integer
     * @uses \phpDocumentor\Reflection\Types\Boolean
     *
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testCompoundCanBeConstructedAndStringifiedCorrectly() : void
    {
        $this->assertSame('int|bool', (string) (new Compound([new Integer(), new Boolean()])));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\Compound::__construct
     * @uses \phpDocumentor\Reflection\Types\Integer
     * @uses \phpDocumentor\Reflection\Types\Boolean
     *
     * @covers ::getIterator
     */
    public function testCompoundCanBeIterated() : void
    {
        $types = [new Integer(), new Boolean()];

        foreach (new Compound($types) as $index => $type) {
            $this->assertSame($types[$index], $type);
        }
    }
}
