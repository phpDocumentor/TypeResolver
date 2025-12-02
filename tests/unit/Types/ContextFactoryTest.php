<?php declare(strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Types {

// Added imports on purpose as mock for the unit tests, please do not remove.
    use phpDocumentor\Reflection\Fqsen as m, phpDocumentor;
    use phpDocumentor\Reflection\DocBlock;
    use phpDocumentor\Reflection\DocBlock\Tag;
    use PHPUnit\Framework\TestCase; // yes, the slash is part of the test
    use PHPUnit\Framework\{
        Assert,
        Exception as e
    };
    use \ReflectionClass;
    use ReflectionClassConstant;
    use ReflectionMethod;
    use ReflectionParameter;
    use ReflectionProperty;
    use stdClass;

    class ContextFactoryTest extends TestCase
    {
        public const TEST_CONSTANT = '';

        public string $testProperty = '';

        /**
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testCreateFromClassReflection() : void
        {
            $fixture = new ContextFactory();
            $context = $fixture->createFromReflector(new ReflectionClass($this));

            $this->assertSame(__NAMESPACE__, $context->getNamespace());
            $this->assertNamespaceAliasesFrom($context);
        }

        /**
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testCreateFromMethodReflection() : void
        {
            $fixture = new ContextFactory();
            $context = $fixture->createFromReflector(new ReflectionMethod($this, 'testCreateFromMethodReflection'));

            $this->assertSame(__NAMESPACE__, $context->getNamespace());
            $this->assertNamespaceAliasesFrom($context);
        }

        /**
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testCreateFromPropertyReflection() : void
        {
            $fixture = new ContextFactory();
            $context = $fixture->createFromReflector(new ReflectionProperty($this, 'testProperty'));

            $this->assertSame(__NAMESPACE__, $context->getNamespace());
            $this->assertNamespaceAliasesFrom($context);
        }

        /**
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testCreateFromClassConstantReflection() : void
        {
            $fixture = new ContextFactory();
            $context = $fixture->createFromReflector(new ReflectionClassConstant($this, 'TEST_CONSTANT'));

            $this->assertSame(__NAMESPACE__, $context->getNamespace());
            $this->assertNamespaceAliasesFrom($context);
        }

        /**
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testCreateFromParameterReflection(): void
        {
            $fixture = new ContextFactory();
            $context = $fixture->createFromReflector(new ReflectionParameter(fn($param) => $param, 'param'));

            $this->assertSame(__NAMESPACE__, $context->getNamespace());
            $this->assertNamespaceAliasesFrom($context);
        }

        /**
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testReadsNamespaceFromProvidedNamespaceAndContent() : void
        {
            $fixture = new ContextFactory();
            $context = $fixture->createForNamespace(__NAMESPACE__, (string) file_get_contents(__FILE__));

            $this->assertSame(__NAMESPACE__, $context->getNamespace());
        }

        /**
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testReadsAliasesFromProvidedNamespaceAndContent() : void
        {
            $fixture = new ContextFactory();
            $context = $fixture->createForNamespace(__NAMESPACE__, (string) file_get_contents(__FILE__));

            $this->assertNamespaceAliasesFrom($context);
        }

        /**
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testTraitUseIsNotDetectedAsNamespaceUse() : void
        {
            $php = '<?php declare(strict_types=1);
                namespace Foo;

                trait FooTrait {}

                class FooClass {
                    use FooTrait;
                }
            ';

            $fixture = new ContextFactory();
            $context = $fixture->createForNamespace('Foo', $php);

            $this->assertSame([], $context->getNamespaceAliases());
        }

        /**
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testAllOpeningBracesAreCheckedWhenSearchingForEndOfClass() : void
        {
            $php = '<?php declare(strict_types=1);
                namespace Foo;

                trait FooTrait {}
                trait BarTrait {}

                class FooClass {
                    use FooTrait;

                    public function bar()
                    {
                        echo "{$baz}";
                        echo "${baz}";
                    }
                }

                class BarClass {
                    use BarTrait;

                    public function bar()
                    {
                        echo "{$baz}";
                        echo "${baz}";
                    }
                }
            ';

            $fixture = new ContextFactory();
            $context = $fixture->createForNamespace('Foo', $php);

            $this->assertSame([], $context->getNamespaceAliases());
        }

        /**
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testTraitContainsClosureWithUseStatement() : void
        {
            $php = '<?php declare(strict_types=1);
                namespace Foo;

                trait FooTrait {
                    protected function check(array $data, string $key) : void
                    {
                        array_walk($data, function(&$item) use ($key) {
                            // update item based on the key
                        });
                    }
                }

                class FooClass {
                    use FooTrait;
                }
            ';

            $fixture = new ContextFactory();
            $context = $fixture->createForNamespace('Foo', $php);

            $this->assertSame([], $context->getNamespaceAliases());
        }

        public function testEmptyFileName() : void
        {
            $fixture = new ContextFactory();
            $context = $fixture->createFromReflector(new ReflectionClass(stdClass::class));

            $this->assertSame([], $context->getNamespaceAliases());
        }

        public function testEvalDClass() : void
        {
            eval(<<<PHP
namespace Foo;

final class Bar
{
}
PHP
);
            $fixture = new ContextFactory();
            $context = $fixture->createFromReflector(new ReflectionClass('Foo\Bar'));

            $this->assertSame([], $context->getNamespaceAliases());
        }

        public function assertNamespaceAliasesFrom(Context $context): void
        {
            $expected = [
                'm' => m::class,
                'DocBlock' => DocBlock::class,
                'Tag' => Tag::class,
                'phpDocumentor' => 'phpDocumentor',
                'TestCase' => TestCase::class,
                'Assert' => Assert::class,
                'e' => e::class,
                ReflectionClass::class => ReflectionClass::class,
                ReflectionMethod::class => ReflectionMethod::class,
                ReflectionProperty::class => ReflectionProperty::class,
                ReflectionClassConstant::class => ReflectionClassConstant::class,
                ReflectionParameter::class => ReflectionParameter::class,
                \stdClass::class => \stdClass::class,
            ];

            $actual = $context->getNamespaceAliases();

            // sort so that order differences don't break it
            asort($expected);
            asort($actual);

            $this->assertSame($expected, $actual);
        }
    }
}

namespace phpDocumentor\Reflection\Types\Mock {

    // the following import should not show in the tests above
    use phpDocumentor\Reflection\Types\AbstractList;

    class Foo extends AbstractList
    {
        // dummy class
    }
}
