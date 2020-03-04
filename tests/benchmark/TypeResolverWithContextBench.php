<?php

declare(strict_types=1);

namespace benchmark;

use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;

/**
 * @BeforeMethods({"setup"})
 */
final class TypeResolverWithContextBench
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var TypeResolver
     */
    private $typeResolver;

    public function setup()
    {
        $factory = new ContextFactory();
        $this->context = $factory->createForNamespace('mpdf', file_get_contents(__DIR__ . '/Assets/mpdf.php'));
        $this->typeResolver = new TypeResolver();
    }

    /**
     * @Warmup(2)
     * @Executor(
     *     "blackfire",
     *     assertions={
     *      {"expression"="main.peak_memory < 11kb", "title"="memory peak"},
     *      "main.wall_time < 1ms"
     *      }
     * )
     */
    public function benchResolveCompoundArrayWithDefinedTypes() : void
    {
        $this->typeResolver->resolve('array<int, string>|array<int, int>', $this->context);
    }

    /**
     * @Warmup(2)
     * @Executor(
     *     "blackfire",
     *     assertions={
     *      {"expression"="main.peak_memory < 11kb", "title"="memory peak"},
     *      "main.wall_time < 1ms"
     *      }
     * )
     */
    public function benchArrayOfClass() : void
    {
        $this->typeResolver->resolve('Conversion[]', $this->context);
    }
}
