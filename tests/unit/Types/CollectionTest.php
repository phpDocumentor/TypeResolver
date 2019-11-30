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

use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Types\Collection
 */
class CollectionTest extends TestCase
{
    /**
     * @dataProvider provideCollections
     * @covers ::__toString
     */
    public function testCollectionStringifyCorrectly(Collection $collection, string $expectedString) : void
    {
        $this->assertSame($expectedString, (string) $collection);
    }

    /**
     * @return mixed[]
     */
    public function provideCollections() : array
    {
        return [
            'simple collection' => [
                new Collection(null, new Integer()),
                'object<int>',
            ],
            'simple collection with key type' => [
                new Collection(null, new Integer(), new String_()),
                'object<string,int>',
            ],
            'collection of single type using specific class' => [
                new Collection(new Fqsen('\Foo\Bar'), new Integer()),
                '\Foo\Bar<int>',
            ],
            'collection of single type with key type and using specific class' => [
                new Collection(new Fqsen('\Foo\Bar'), new String_(), new Integer()),
                '\Foo\Bar<int,string>',
            ],
        ];
    }
}
