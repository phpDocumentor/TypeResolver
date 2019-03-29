<?php

namespace phpDocumentor\Reflection\unit;

use phpDocumentor\Reflection\FqsenResolver;
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
    public function testResolveFqsen()
    {
        $fqsenResolver = new FqsenResolver();

        $context = new Context('', []);

        $result = $fqsenResolver->resolve('\DocBlock', $context);
        static::assertEquals('\DocBlock', (string)$result);
    }

    /**
     * @covers ::resolve
     */
    public function testResolveWithoutContext()
    {
        $fqsenResolver = new FqsenResolver();

        $result = $fqsenResolver->resolve('\DocBlock');
        static::assertEquals('\DocBlock', (string)$result);
    }

    /**
     * @covers ::resolve
     */
    public function testResolveFromAlias()
    {
        $fqsenResolver = new FqsenResolver();

        $context = new Context('somens', ['ns' => 'some\other\ns']);

        $result = $fqsenResolver->resolve('ns', $context);
        static::assertEquals('\some\other\ns', (string)$result);
    }

    /**
     * @covers ::resolve
     */
    public function testResolveFromPartialAlias()
    {
        $fqsenResolver = new FqsenResolver();

        $context = new Context('somens', ['other' => 'some\other']);

        $result = $fqsenResolver->resolve('other\ns', $context);
        static::assertEquals('\some\other\ns', (string)$result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testResolveThrowsExceptionWhenGarbageInputIsPassed()
    {
        $fqsenResolver = new FqsenResolver();

        $context = new Context('', []);

        $fqsenResolver->resolve('this is complete garbage', $context);
    }
}
