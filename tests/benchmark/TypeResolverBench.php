<?php

declare(strict_types=1);

namespace benchmark;

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
     *      {"expression"="main.peak_memory < 11kb", "title"="memory peak"},
     *      "main.wall_time < 300us"
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
     * @Executor(
     *     "blackfire",
     *     assertions={
     *      {"expression"="main.peak_memory < 11kb", "title"="memory peak"},
     *      "main.wall_time < 0.5ms"
     *      }
     * )
     */
    public function benchResolveCompoundType() : void
    {
        $this->typeResolver->resolve('string|int|bool');
    }

    /**
     * @Warmup(2)
     * @Revs(10000)
     * @Executor(
     *     "blackfire",
     *     assertions={
     *      {"expression"="main.peak_memory < 11kb", "title"="memory peak"},
     *      "main.wall_time < 300us"
     *      }
     * )
     */
    public function benchResolveArrayType() : void
    {
        $this->typeResolver->resolve('string[]');
    }

    /**
     * @Warmup(2)
     * @Revs(10000)
     * @Executor(
     *     "blackfire",
     *     assertions={
     *      {"expression"="main.peak_memory < 11kb", "title"="memory peak"},
     *      "main.wall_time < 300us"
     *      }
     * )
     */
    public function benchResolveCompoundArrayType() : void
    {
        $this->typeResolver->resolve('(string|int)[]');
    }

    /**
     * @Warmup(2)
     * @Revs(10000)
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
        $this->typeResolver->resolve('array<int, string>|array<int, int>');
    }
}
