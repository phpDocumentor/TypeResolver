<?php

use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Resolver;

require '../vendor/autoload.php';

$typeResolver = new Resolver();

// Will use the namespace and aliases to resolve to \phpDocumentor\Types\Resolver|Mockery\MockInterface
$context = new Context('\phpDocumentor', [ 'm' => 'Mockery' ]);
var_dump((string)$typeResolver->resolveType('Types\Resolver|m\MockInterface', $context));
