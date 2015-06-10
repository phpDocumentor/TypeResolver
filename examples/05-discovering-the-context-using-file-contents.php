<?php

use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Resolver;

require '../vendor/autoload.php';

$typeResolver = new Resolver();
$contextFactory = new ContextFactory();
$context = $contextFactory->createForNamespace('My\Example', file_get_contents('Classy.php'));

// Class named: \phpDocumentor\Reflection\Types\Resolver
var_dump((string)$typeResolver->resolveType('Types\Resolver', $context));

// String
var_dump((string)$typeResolver->resolveType('string', $context));

// Property named: \phpDocumentor\Reflection\Types\Resolver::$keyWords
var_dump((string)$typeResolver->resolveFqsen('Types\Resolver::$keyWords', $context));
