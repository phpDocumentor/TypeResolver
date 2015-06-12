<?php

use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\ContextFactory;

require '../vendor/autoload.php';
require 'Classy.php';

$typeResolver = new TypeResolver();
$fqsenResolver = new FqsenResolver();

$contextFactory = new ContextFactory();
$context = $contextFactory->createFromReflector(new ReflectionMethod('My\\Example\\Classy', '__construct'));

// Class named: \phpDocumentor\Reflection\Types\Resolver
var_dump((string)$typeResolver->resolve('Types\Resolver', $context));

// String
var_dump((string)$typeResolver->resolve('string', $context));

// Property named: \phpDocumentor\Reflection\Types\Resolver::$keyWords
var_dump((string)$fqsenResolver->resolve('Types\Resolver::$keyWords', $context));

// Class named: \My\Example\string
// - Shows the difference between the FqsenResolver and TypeResolver; the FqsenResolver will assume
//   that the given value is not a type but most definitely a reference to another element. This is
//   because conflicts between type keywords and class names can exist and if you know a reference
//   is not a type but an element you can force that keywords are resolved.
var_dump((string)$fqsenResolver->resolve('string', $context));
