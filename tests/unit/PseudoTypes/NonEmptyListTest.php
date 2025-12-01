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

use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

class NonEmptyListTest extends TestCase
{
    public function testCreateWithoutParams(): void
    {
        $type = new NonEmptyList();

        $this->assertEquals(new Integer(), $type->getOriginalKeyType());
        $this->assertNull($type->getOriginalValueType());
        $this->assertEquals(new Integer(),  $type->getKeyType());
        $this->assertEquals(new Mixed_(), $type->getValueType());
        $this->assertEquals(new Array_(null, new Integer()), $type->underlyingType());
    }

    /**
     * @dataProvider provideToStringData
     */
    public function testToString(NonEmptyList $array, string $expectedString): void
    {
        $this->assertSame($expectedString, (string) $array);
    }

    /**
     * @return array<string, array{NonEmptyList, string}>
     */
    public function provideToStringData(): array
    {
        return [
            'simple non-empty-list' => [new NonEmptyList(), 'non-empty-list'],
            'non-empty-list of mixed' => [new NonEmptyList(new Mixed_()), 'non-empty-list<mixed>'],
            'non-empty-list of single type' => [new NonEmptyList(new String_()), 'non-empty-list<string>'],
            'non-empty-list of compound type' =>
                [new NonEmptyList(new Compound([new Integer(), new String_()])), 'non-empty-list<int|string>'],
        ];
    }
}
