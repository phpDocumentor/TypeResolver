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
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\False_
 */
final class FalseTest extends TestCase
{
    /**
     * @covers ::underlyingType
     */
    public function testExposesUnderlyingType() : void
    {
        $false = new False_();

        $this->assertInstanceOf(Boolean::class, $false->underlyingType());
    }

    /**
     * @covers ::__toString
     */
    public function testFalseStringifyCorrectly() : void
    {
        $false = new False_();

        $this->assertSame('false', (string) $false);
    }

    /**
     * @covers \phpDocumentor\Reflection\PseudoTypes\False_
     */
    public function testCanBeInstantiatedUsingDeprecatedFqsen() : void
    {
        $false = new \phpDocumentor\Reflection\Types\False_();

        $this->assertSame('false', (string) $false);
        $this->assertInstanceOf(False_::class, $false);
        $this->assertInstanceOf(\phpDocumentor\Reflection\Types\False_::class, $false);
    }
}
