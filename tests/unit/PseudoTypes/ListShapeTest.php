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

class ListShapeTest extends TestCase
{
    public function testToString(): void
    {
        $type = new ListShape(new ListShapeItem(null, new IntegerValue(1), false));

        $this->assertSame('list{1}', (string) $type);
    }
}
