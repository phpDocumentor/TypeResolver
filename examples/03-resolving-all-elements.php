<?php

use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\FqsenResolver;

require '../vendor/autoload.php';

$fqsenResolver = new FqsenResolver();

// Will use the namespace and aliases to resolve to a Fqsen object
$context = new Context('\phpDocumentor\Types');

// Method named: \phpDocumentor\Types\Types\Resolver::resolveFqsen()
var_dump((string)$fqsenResolver->resolve('Types\Resolver::resolveFqsen()', $context));

// Property named: \phpDocumentor\Types\Types\Resolver::$keyWords
var_dump((string)$fqsenResolver->resolve('Types\Resolver::$keyWords', $context));
