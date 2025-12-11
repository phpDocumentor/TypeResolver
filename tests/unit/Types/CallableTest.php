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

final class CallableTest extends TestCase
{
    public function testCreate(): void
    {
        $identifier = 'callable';
        $parameters = [
            new CallableParameter(
                new Object_(new Fqsen('\\phpDocumentor\\A')),
                'a',
                true,
                true,
                true
            ),
            new CallableParameter(
                new Object_(new Fqsen('\\phpDocumentor\\B')),
                null,
                true,
                true,
                true
            ),
            new CallableParameter(
                new Object_(new Fqsen('\\phpDocumentor\\C')),
                null,
                false,
                false,
                false
            ),
        ];

        $returnType = new Object_(new Fqsen('\\phpDocumentor\\Foo'));

        $type = new Callable_($identifier, $parameters, $returnType);

        $this->assertSame($identifier, $type->getIdentifier());
        $this->assertSame($parameters, $type->getParameters());
        $this->assertSame($returnType, $type->getReturnType());
    }

    /**
     * @dataProvider provideToStringData
     */
    public function testToString(string $expectedResult, Callable_ $type): void
    {
        $this->assertSame($expectedResult, (string) $type);
    }

    /**
     * @return array<string, array{string, Callable_}>
     */
    public static function provideToStringData(): array
    {
        return [
            'basic' => [
                'callable',
                new Callable_(),
            ],
            'pure' => [
                'pure-callable(int): int',
                new Callable_(
                    'pure-callable',
                    [new CallableParameter(new Integer())],
                    new Integer()
                ),
            ],
            'closure' => [
                '\Closure',
                new Callable_('\Closure'),
            ],
            'with different types' => [
                'callable(\\phpDocumentor\\C, \\phpDocumentor\\A &...$a=, \\phpDocumentor\\B &...=): '
                    . '\\phpDocumentor\\Foo',
                new Callable_(
                    'callable',
                    [
                        new CallableParameter(
                            new Object_(new Fqsen('\\phpDocumentor\\C')),
                            null,
                            false,
                            false,
                            false
                        ),
                        new CallableParameter(
                            new Object_(new Fqsen('\\phpDocumentor\\A')),
                            'a',
                            true,
                            true,
                            true
                        ),
                        new CallableParameter(
                            new Object_(new Fqsen('\\phpDocumentor\\B')),
                            null,
                            true,
                            true,
                            true
                        ),
                    ],
                    new Object_(new Fqsen('\\phpDocumentor\\Foo'))
                ),
            ],
            'return callable' => [
                'Closure(mixed): (callable(mixed): mixed)',
                new Callable_(
                    'Closure',
                    [
                        new CallableParameter(
                            new Mixed_(),
                            null,
                            false,
                            false,
                            false
                        ),
                    ],
                    new Callable_(
                        'callable',
                        [
                            new CallableParameter(
                                new Mixed_(),
                                null,
                                false,
                                false,
                                false
                            ),
                        ],
                        new Mixed_()
                    )
                ),
            ],
        ];
    }
}
