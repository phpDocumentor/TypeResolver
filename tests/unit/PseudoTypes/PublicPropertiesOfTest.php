<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Types\Self_;
use PHPUnit\Framework\TestCase;

final class PublicPropertiesOfTest extends TestCase
{
    public function testToString(): void
    {
        $type = new PublicPropertiesOf(new Self_());

        $this->assertSame('public-properties-of<self>', (string) $type);
    }
}
