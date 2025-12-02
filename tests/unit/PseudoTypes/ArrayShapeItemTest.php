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

use PHPUnit\Framework\TestCase;

final class ArrayShapeItemTest extends TestCase
{
    public function testCreate(): void
    {
        $key = 'abc';
        $value = new IntegerValue(100);
        $item = new ArrayShapeItem($key, $value, true);

        $this->assertSame($key, $item->getKey());
        $this->assertSame($value, $item->getValue());
        $this->assertTrue($item->isOptional());
    }
}
