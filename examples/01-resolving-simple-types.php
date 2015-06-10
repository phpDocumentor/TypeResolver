<?php

use phpDocumentor\Reflection\Types\Resolver;

require '../vendor/autoload.php';

$typeResolver = new Resolver();

// Will yield an object of type phpDocumentor\Types\Compound
var_export($typeResolver->resolveType('string|integer'));

// Will return the string "string|int"
var_dump((string)$typeResolver->resolveType('string|integer'));
