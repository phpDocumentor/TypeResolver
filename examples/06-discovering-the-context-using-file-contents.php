<?php

use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\ContextFactory;

require '../vendor/autoload.php';

$typeResolver = new TypeResolver();
$fqsenResolver = new FqsenResolver();

$contextFactory = new ContextFactory();
$context = $contextFactory->createForNamespace('My\Example', file_get_contents('Classy.php'));

// Class named: \phpDocumentor\Reflection\Types\Resolver
var_dump((string)$typeResolver->resolve('Types\Resolver', $context));

// String
var_dump((string)$typeResolver->resolve('string', $context));

// Property named: \phpDocumentor\Reflection\Types\Resolver::$keyWords
var_dump((string)$fqsenResolver->resolve('Types\Resolver::$keyWords', $context));
