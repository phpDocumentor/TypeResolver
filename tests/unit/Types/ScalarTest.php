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

final class ScalarTest extends TestCase
{
    public function testToString(): void
    {
        $type = new Scalar();

        $this->assertSame('scalar', (string) $type);
    }
}
