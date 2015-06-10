<?php

use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Resolver;

require '../vendor/autoload.php';
require 'Classy.php';

$typeResolver = new Resolver();
$contextFactory = new ContextFactory();
$context = $contextFactory->createFromClassReflector(new ReflectionClass('My\\Example\\Classy'));

// Class named: \phpDocumentor\Reflection\Types\Resolver
var_dump((string)$typeResolver->resolveType('Types\Resolver', $context));

// String
var_dump((string)$typeResolver->resolveType('string', $context));

// Property named: \phpDocumentor\Reflection\Types\Resolver::$keyWords
var_dump((string)$typeResolver->resolveFqsen('Types\Resolver::$keyWords', $context));
