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

namespace phpDocumentor\Reflection;

use InvalidArgumentException;
use phpDocumentor\Reflection\Types\Context;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\FqsenResolver
 * @covers ::<private>
 */
class FqsenResolverTest extends TestCase
{
    /**
     * @covers ::resolve
     */
    public function testResolveFqsen() : void
    {
        $fqsenResolver = new FqsenResolver();

        $context = new Context('', []);

        $result = $fqsenResolver->resolve('\DocBlock', $context);
        static::assertEquals('\DocBlock', (string) $result);
    }

    /**
     * @covers ::resolve
     */
    public function testResolveWithoutContext() : void
    {
        $fqsenResolver = new FqsenResolver();

        $result = $fqsenResolver->resolve('\DocBlock');
        static::assertEquals('\DocBlock', (string) $result);
    }

    /**
     * @covers ::resolve
     */
    public function testResolveFromAlias() : void
    {
        $fqsenResolver = new FqsenResolver();

        $context = new Context('somens', ['ns' => 'some\other\ns']);

        $result = $fqsenResolver->resolve('ns', $context);
        static::assertEquals('\some\other\ns', (string) $result);
    }

    /**
     * @covers ::resolve
     */
    public function testResolveFromPartialAlias() : void
    {
        $fqsenResolver = new FqsenResolver();

        $context = new Context('somens', ['other' => 'some\other']);

        $result = $fqsenResolver->resolve('other\ns', $context);
        static::assertEquals('\some\other\ns', (string) $result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testResolveThrowsExceptionWhenGarbageInputIsPassed() : void
    {
        $fqsenResolver = new FqsenResolver();

        $context = new Context('', []);

        $fqsenResolver->resolve('this is complete garbage', $context);
    }
}
