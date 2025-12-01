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

final class FqsenResolverTest extends TestCase
{
    public function testResolveFqsen(): void
    {
        $fqsenResolver = new FqsenResolver();

        $context = new Context('', []);

        $result = $fqsenResolver->resolve('\DocBlock', $context);
        static::assertSame('\DocBlock', (string) $result);
    }

    public function testResolveFqsenWithEmoji(): void
    {
        $fqsenResolver = new FqsenResolver();

        $context = new Context('', []);

        $result = $fqsenResolver->resolve('\My😁DocBlock', $context);
        static::assertSame('\My😁DocBlock', (string) $result);
    }

    public function testResolveWithoutContext(): void
    {
        $fqsenResolver = new FqsenResolver();

        $result = $fqsenResolver->resolve('\DocBlock');
        static::assertSame('\DocBlock', (string) $result);
    }

    public function testResolveFromAlias(): void
    {
        $fqsenResolver = new FqsenResolver();

        $context = new Context('somens', ['ns' => 'some\other\ns']);

        $result = $fqsenResolver->resolve('ns', $context);
        static::assertSame('\some\other\ns', (string) $result);
    }

    public function testResolveFromPartialAlias(): void
    {
        $fqsenResolver = new FqsenResolver();

        $context = new Context('somens', ['other' => 'some\other']);

        $result = $fqsenResolver->resolve('other\ns', $context);
        static::assertSame('\some\other\ns', (string) $result);
    }

    public function testResolveThrowsExceptionWhenGarbageInputIsPassed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $fqsenResolver = new FqsenResolver();

        $context = new Context('', []);

        $fqsenResolver->resolve('this is complete garbage', $context);
    }
}
