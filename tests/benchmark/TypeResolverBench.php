<?php

declare(strict_types=1);

namespace benchmark;

use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use phpDocumentor\Reflection\TypeResolver;

/**
 * @BeforeMethods({"setup"})
 */
class TypeResolverBench
{
    private TypeResolver $typeResolver;

    public function setup()
    {
        $this->typeResolver = new TypeResolver();
    }

    /**
     * @Warmup(2)
     * @Revs(10000)
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
