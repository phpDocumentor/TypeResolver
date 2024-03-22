<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\PseudoTypes\ArrayShape
 */
class ArrayShapeTest extends TestCase
{
    /**
     * @covers ::getItems
     */
    public function testExposeItems(): void
    {
        $item1 = new ArrayShapeItem('foo', new True_(), false);
        $item2 = new ArrayShapeItem('bar', new False_(), true);

        $arrayShape = new ArrayShape($item1, $item2);

        $this->assertSame([$item1, $item2], $arrayShape->getItems());
    }
}
