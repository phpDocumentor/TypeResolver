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

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Nullable
 */
class NullableTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getActualType
     */
    public function testNullableTypeWrapsCorrectly() : void
    {
        $realType = new String_();

        $nullableString = new Nullable($realType);

        $this->assertSame($realType, $nullableString->getActualType());
    }

    /**
     * @covers ::__toString
     */
    public function testNullableStringifyCorrectly() : void
    {
        $this->assertSame('?string', (string) new Nullable(new String_()));
    }
}
