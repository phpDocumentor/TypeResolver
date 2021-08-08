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
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Boolean
 */
final class BooleanTest extends TestCase
{
    /**
     * @covers ::__toString
     */
    public function testBooleanStringifyCorrectly(): void
    {
        $type = new Boolean();

        $this->assertSame('bool', (string) $type);
    }
}
