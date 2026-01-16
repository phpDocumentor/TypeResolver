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

use phpDocumentor\Reflection\Types\Integer;
use PHPUnit\Framework\TestCase;

final class NonPositiveIntegerTest extends TestCase
{
    public function testCreate(): void
    {
        $type = new NonPositiveInteger();
        $this->assertEquals(new Integer(), $type->underlyingType());
    }

    public function testToString(): void
    {
        $this->assertSame('non-positive-int', (string) (new NonPositiveInteger()));
    }
}
