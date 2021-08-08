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

/**
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\True_
 */
class TrueTest extends TestCase
{
    /**
     * @covers ::underlyingType
     */
    public function testExposesUnderlyingType(): void
    {
        $true = new True_();

        $this->assertInstanceOf(Boolean::class, $true->underlyingType());
    }

    /**
     * @covers ::__toString
     */
    public function testTrueStringifyCorrectly(): void
    {
        $true = new True_();

        $this->assertSame('true', (string) $true);
    }

    /**
     * @covers \phpDocumentor\Reflection\PseudoTypes\True_
     */
    public function testCanBeInstantiatedUsingDeprecatedFqsen(): void
    {
        $true = new \phpDocumentor\Reflection\Types\True_();

        $this->assertSame('true', (string) $true);
        $this->assertInstanceOf(True_::class, $true);
        $this->assertInstanceOf(\phpDocumentor\Reflection\Types\True_::class, $true);
    }
}
