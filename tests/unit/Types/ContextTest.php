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
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Context
 */
class ContextTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getNamespace
     */
    public function testProvidesANormalizedNamespace() : void
    {
        $fixture = new Context('\My\Space');
        $this->assertSame('My\Space', $fixture->getNamespace());
    }

    /**
     * @covers ::__construct
     * @covers ::getNamespace
     */
    public function testInterpretsNamespaceNamedGlobalAsRootNamespace() : void
    {
        $fixture = new Context('global');
        $this->assertSame('', $fixture->getNamespace());
    }

    /**
     * @covers ::__construct
     * @covers ::getNamespace
     */
    public function testInterpretsNamespaceNamedDefaultAsRootNamespace() : void
    {
        $fixture = new Context('default');
        $this->assertSame('', $fixture->getNamespace());
    }

    /**
     * @covers ::__construct
     * @covers ::getNamespaceAliases
     */
    public function testProvidesNormalizedNamespaceAliases() : void
    {
        $fixture = new Context('', ['Space' => '\My\Space']);
        $this->assertSame(['Space' => 'My\Space'], $fixture->getNamespaceAliases());
    }
}
