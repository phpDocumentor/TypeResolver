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

final class TrueTest extends TestCase
{
    public function testExposesUnderlyingType(): void
    {
        $true = new True_();

        $this->assertInstanceOf(Boolean::class, $true->underlyingType());
    }

    public function testTrueStringifyCorrectly(): void
    {
        $true = new True_();

        $this->assertSame('true', (string) $true);
    }
}
