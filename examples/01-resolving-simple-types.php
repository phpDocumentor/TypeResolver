<?php

use phpDocumentor\Reflection\TypeResolver;

require __DIR__ . '/../vendor/autoload.php';

$typeResolver = new TypeResolver();

// Will yield an object of type phpDocumentor\Types\Compound
var_export($typeResolver->resolve('string|integer'));

// Will return the string "string|int"
var_dump((string)$typeResolver->resolve('string|integer'));
