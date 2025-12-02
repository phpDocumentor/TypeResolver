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

final class NeverTest extends TestCase
{
    public function testToString(): void
    {
        $type = new Never_();

        $this->assertSame('never', (string) $type);
    }
}
