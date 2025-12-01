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

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

final class ArrayKeyTest extends TestCase
{
    public function testCreate(): void
    {
        $type = new ArrayKey();

        $this->assertEquals(new Compound([new String_(), new Integer()]), $type->underlyingType());
    }

    public function testToString(): void
    {
        $this->assertSame('array-key', (string) (new ArrayKey()));
    }

    /**
     * @uses \phpDocumentor\Reflection\PseudoTypes\ArrayKey::__construct
     */
    public function testArrayKeyCanBeIterated(): void
    {
        $types = [String_::class, Integer::class];

        foreach (new ArrayKey() as $index => $type) {
            $this->assertInstanceOf($types[$index], $type);
        }
    }
}
