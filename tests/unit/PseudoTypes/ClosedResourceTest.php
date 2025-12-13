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

use phpDocumentor\Reflection\Types\Resource_;
use PHPUnit\Framework\TestCase;

final class ClosedResourceTest extends TestCase
{
    public function testCreate(): void
    {
        $type = new ClosedResource();

        $this->assertEquals(new Resource_(), $type->underlyingType());
    }

    public function testToString(): void
    {
        $this->assertSame('closed-resource', (string) (new ClosedResource()));
    }
}
