<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Types;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Compound
 */
class CompoundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage A compound type can only have other types as elements
     */
    public function testCompoundCannotBeConstructedFromType()
    {
        new Compound(['foo']);
    }

    /**
     * @covers ::get
     *
     * @uses \phpDocumentor\Reflection\Types\Compound::__construct
     * @uses \phpDocumentor\Reflection\Types\Compound::has
     * @uses \phpDocumentor\Reflection\Types\Integer
     */
    public function testCompoundGetType()
    {
        $integer = new Integer();

        $this->assertSame($integer, (new Compound([$integer]))->get(0));
    }

    /**
     * @covers ::get
     *
     * @uses \phpDocumentor\Reflection\Types\Compound::__construct
     * @uses \phpDocumentor\Reflection\Types\Compound::has
     */
    public function testCompoundGetNotExistingType()
    {
        $this->assertNull((new Compound([]))->get(0));
    }

    /**
     * @covers ::has
     *
     * @uses \phpDocumentor\Reflection\Types\Compound::__construct
     * @uses \phpDocumentor\Reflection\Types\Integer
     */
    public function testCompoundHasType()
    {
        $this->assertTrue((new Compound([new Integer()]))->has(0));
    }

    /**
     * @covers ::has
     *
     * @uses \phpDocumentor\Reflection\Types\Compound::__construct
     */
    public function testCompoundHasNotExistingType()
    {
        $this->assertFalse((new Compound([]))->has(0));
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     *
     * @uses \phpDocumentor\Reflection\Types\Integer
     * @uses \phpDocumentor\Reflection\Types\Boolean
     */
    public function testCompoundCanBeConstructedAndStringifiedCorrectly()
    {
        $this->assertSame('int|bool', (string)(new Compound([new Integer(), new Boolean()])));
    }

    /**
     * @covers ::getIterator
     *
     * @uses \phpDocumentor\Reflection\Types\Compound::__construct
     * @uses \phpDocumentor\Reflection\Types\Integer
     * @uses \phpDocumentor\Reflection\Types\Boolean
     */
    public function testCompoundCanBeIterated()
    {
        $types = [new Integer(), new Boolean()];

        foreach (new Compound($types) as $index => $type) {
            $this->assertSame($types[$index], $type);
        }
    }
}
