<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Types\Self_;
use PHPUnit\Framework\TestCase;

final class ProtectedPropertiesOfTest extends TestCase
{
    public function testToString(): void
    {
        $type = new ProtectedPropertiesOf(new Self_());

        $this->assertSame('protected-properties-of<self>', (string) $type);
    }
}
