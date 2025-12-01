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

use phpDocumentor\Reflection\Types\Boolean;
use PHPUnit\Framework\TestCase;

final class FalseTest extends TestCase
{
    public function testExposesUnderlyingType(): void
    {
        $false = new False_();

        $this->assertInstanceOf(Boolean::class, $false->underlyingType());
    }

    public function testFalseStringifyCorrectly(): void
    {
        $false = new False_();

        $this->assertSame('false', (string) $false);
    }
}
