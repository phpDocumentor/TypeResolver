<?php

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\PseudoTypes\ArrayShape;
use phpDocumentor\Reflection\PseudoTypes\ArrayShapeItem;
use phpDocumentor\Reflection\PseudoTypes\False_;
use phpDocumentor\Reflection\PseudoTypes\True_;
use PHPUnit\Framework\TestCase;

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