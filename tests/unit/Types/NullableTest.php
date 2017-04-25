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
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Nullable
 */
class NullableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getActualType
     */
    public function testNullableTypeWrapsCorrectly()
    {
        $realType = new String_();

        $nullableString = new Nullable($realType);

        $this->assertSame($realType, $nullableString->getActualType());
    }

    /**
     * @covers ::__toString
     */
    public function testNullableStringifyCorrectly()
    {
        $this->assertSame('?string', (string)(new Nullable(new String_())));
    }
}
