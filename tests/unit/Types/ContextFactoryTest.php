<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Types {

// Added imports on purpose as mock for the unit tests, please do not remove.
    use Mockery as m;
    use phpDocumentor\Reflection\DocBlock,
        phpDocumentor\Reflection\DocBlock\Tag;
    use phpDocumentor;
    use \ReflectionClass; // yes, the slash is part of the test

    /**
     * @coversDefaultClass \phpDocumentor\Reflection\Types\ContextFactory
     * @covers ::<private>
     */
    class ContextFactoryTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @covers ::createFromReflector
         * @covers ::createForNamespace
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testReadsNamespaceFromClassReflection()
        {
            $fixture = new ContextFactory();
            $context = $fixture->createFromReflector(new ReflectionClass($this));

            $this->assertSame(__NAMESPACE__, $context->getNamespace());
        }

        /**
         * @covers ::createFromReflector
         * @covers ::createForNamespace
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testReadsAliasesFromClassReflection()
        {
            $fixture = new ContextFactory();
            $expected = [
                'm' => 'Mockery',
                'DocBlock' => 'phpDocumentor\Reflection\DocBlock',
                'Tag' => 'phpDocumentor\Reflection\DocBlock\Tag',
                'phpDocumentor' => 'phpDocumentor',
                'ReflectionClass' => 'ReflectionClass'
            ];
            $context = $fixture->createFromReflector(new ReflectionClass($this));

            $this->assertSame($expected, $context->getNamespaceAliases());
        }

        /**
         * @covers ::createForNamespace
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testReadsNamespaceFromProvidedNamespaceAndContent()
        {
            $fixture = new ContextFactory();
            $context = $fixture->createForNamespace(__NAMESPACE__, file_get_contents(__FILE__));

            $this->assertSame(__NAMESPACE__, $context->getNamespace());
        }

        /**
         * @covers ::createForNamespace
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testReadsAliasesFromProvidedNamespaceAndContent()
        {
            $fixture = new ContextFactory();
            $expected = [
                'm'               => 'Mockery',
                'DocBlock'        => 'phpDocumentor\Reflection\DocBlock',
                'Tag'             => 'phpDocumentor\Reflection\DocBlock\Tag',
                'phpDocumentor' => 'phpDocumentor',
                'ReflectionClass' => 'ReflectionClass'
            ];
            $context = $fixture->createForNamespace(__NAMESPACE__, file_get_contents(__FILE__));

            $this->assertSame($expected, $context->getNamespaceAliases());
        }

        /**
         * @covers ::createForNamespace
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testTraitUseIsNotDetectedAsNamespaceUse()
        {
            $php = "<?php
                namespace Foo;

                trait FooTrait {}

                class FooClass {
                    use FooTrait;
                }
            ";

            $fixture = new ContextFactory();
            $context = $fixture->createForNamespace('Foo', $php);

            $this->assertSame([], $context->getNamespaceAliases());
        }

        /**
         * @covers ::createForNamespace
         * @uses phpDocumentor\Reflection\Types\Context
         */
        public function testAllOpeningBracesAreCheckedWhenSearchingForEndOfClass()
        {
            $php = '<?php
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
         * @covers ::createFromReflector
         */
        public function testEmptyFileName()
        {
            $fixture = new ContextFactory();
            $context = $fixture->createFromReflector(new \ReflectionClass('stdClass'));

            $this->assertSame([], $context->getNamespaceAliases());
        }

        /**
         * @covers ::createFromReflector
         */
        public function testEvalDClass()
        {
            eval(<<<PHP
namespace Foo;

class Bar
{
}
PHP
);
            $fixture = new ContextFactory();
            $context = $fixture->createFromReflector(new \ReflectionClass('Foo\Bar'));

            $this->assertSame([], $context->getNamespaceAliases());
        }
    }
}

namespace phpDocumentor\Reflection\Types\Mock {
    // the following import should not show in the tests above
    use phpDocumentor\Reflection\DocBlock\Description;
}
