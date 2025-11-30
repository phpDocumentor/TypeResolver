<?php

declare(strict_types=1);

namespace benchmark;

use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use phpDocumentor\Reflection\Types\ContextFactory;

/**
 * @BeforeMethods({"setup"})
 */
final class ContextFactoryBench
{
    private string $source;

    public function setup(): void
    {
        $this->source = (string) file_get_contents(__DIR__ . '/Assets/mpdf.php');
    }

    /**
     * @Warmup(1)
     */
    public function benchCreateContextForNamespace(): void
    {
        $factory = new ContextFactory();
        $factory->createForNamespace(
            'Mpdf',
            $this->source
        );
    }
}
