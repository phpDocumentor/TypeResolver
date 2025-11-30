<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\Closure\ClosureReturnTypeRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests/unit',
    ])
    ->withImportNames()
    ->withPhpVersion(PhpVersion::PHP_74)
    ->withPHPStanConfigs([
        __DIR__ . '/phpstan.neon',
    ])
    ->withRules([
        InlineConstructorDefaultToPropertyRector::class,
        CompleteDynamicPropertiesRector::class,
        ClosureReturnTypeRector::class,
    ]);
