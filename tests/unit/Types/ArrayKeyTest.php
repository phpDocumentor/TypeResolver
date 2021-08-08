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
 * @coversDefaultClass \phpDocumentor\Reflection\Types\ArrayKey
 */
final class ArrayKeyTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testArrayKeyCanBeConstructedAndStringifiedCorrectly(): void
    {
        $this->assertSame('array-key', (string) (new ArrayKey()));
    }

    /**
     * @uses \phpDocumentor\Reflection\Types\ArrayKey::__construct
     *
     * @covers ::getIterator
     */
    public function testArrayKeyCanBeIterated(): void
    {
        $types = [String_::class, Integer::class];

        foreach (new ArrayKey() as $index => $type) {
            $this->assertInstanceOf($types[$index], $type);
        }
    }
}
