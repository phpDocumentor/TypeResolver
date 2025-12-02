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

final class ContextTest extends TestCase
{
    public function testProvidesANormalizedNamespace(): void
    {
        $fixture = new Context('\My\Space');
        $this->assertSame('My\Space', $fixture->getNamespace());
    }

    public function testInterpretsNamespaceNamedGlobalAsRootNamespace(): void
    {
        $fixture = new Context('global');
        $this->assertSame('', $fixture->getNamespace());
    }

    public function testInterpretsNamespaceNamedDefaultAsRootNamespace(): void
    {
        $fixture = new Context('default');
        $this->assertSame('', $fixture->getNamespace());
    }

    public function testProvidesNormalizedNamespaceAliases(): void
    {
        $fixture = new Context('', ['Space' => '\My\Space']);
        $this->assertSame(['Space' => 'My\Space'], $fixture->getNamespaceAliases());

        $fixture = new Context('', ['Space' => '\My\Space\\']);
        $this->assertSame(['Space' => 'My\Space'], $fixture->getNamespaceAliases());
    }
}
