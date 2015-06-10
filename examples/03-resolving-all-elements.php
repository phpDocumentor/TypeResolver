<?php

use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Resolver;

require '../vendor/autoload.php';

$typeResolver = new Resolver();

// Will use the namespace and aliases to resolve to a Fqsen object
$context = new Context('\phpDocumentor\Types');

// Method named: \phpDocumentor\Types\Types\Resolver::resolveFqsen()
var_dump((string)$typeResolver->resolveFqsen('Types\Resolver::resolveFqsen()', $context));

// Property named: \phpDocumentor\Types\Types\Resolver::$keyWords
var_dump((string)$typeResolver->resolveFqsen('Types\Resolver::$keyWords', $context));
