<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Types\Self_;
use PHPUnit\Framework\TestCase;

final class PrivatePropertiesOfTest extends TestCase
{
    public function testToString(): void
    {
        $type = new PrivatePropertiesOf(new Self_());

        $this->assertSame('private-properties-of<self>', (string) $type);
    }
}
