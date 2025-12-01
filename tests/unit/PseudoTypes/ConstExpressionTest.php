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

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;

class ConstExpressionTest extends TestCase
{
    public function testCreate(): void
    {
        $owner = new Object_(new Fqsen('\Foo\Bar'));
        $expression = '*';
        $type = new ConstExpression($owner, $expression);

        $this->assertSame($owner, $type->getOwner());
        $this->assertSame($expression, $type->getExpression());
        $this->assertEquals(new Mixed_(), $type->underlyingType());
    }
}
