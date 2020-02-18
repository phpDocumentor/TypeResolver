<?php

declare(strict_types=1);

namespace benchmark;

use Blackfire\Profile\Configuration;
use PhpBench\Benchmark\Metadata\Annotations\Assert;
use phpDocumentor\Reflection\TypeResolver;

/**
 * @BeforeMethods({"setup"})
 */
class TypeResolverBench
{
    private $typeResolver;

    public function setup()
    {
        $this->typeResolver = new TypeResolver();
    }

    /**
     * @Warmup(2)
     * @Revs(10000)
     * @Executor(
     *     "blackfire",
     *     assertions={
     *      {"expression"="main.peak_memory < 10mb", "title"="test"},
     *      "main.wall_time < 50ms"
     *      }
     * )
     */
    public function benchResolveSingleType() : void
    {
        $this->typeResolver->resolve('string');
    }

    /**
     * @Warmup(2)
     * @Revs(10000)
     */
    public function benchResolveCompoundType() : void
    {
        $this->typeResolver->resolve('string|int|bool');
    }

    /**
     * @Warmup(2)
     * @Revs(10000)
     */
    public function benchResolveArrayType() : void
    {
        $this->typeResolver->resolve('string[]');
    }

    /**
     * @Warmup(2)
     * @Revs(10000)
     */
    public function benchResolveCompoundArrayType() : void
    {
        $this->typeResolver->resolve('(string|int)[]');
    }

    /**
     * @Warmup(2)
     * @Revs(10000)
     */
    public function benchResolveCompoundArrayWithDefinedTypes() : void
    {
        $this->typeResolver->resolve('array<int, string>|array<int, int>');
    }
}
