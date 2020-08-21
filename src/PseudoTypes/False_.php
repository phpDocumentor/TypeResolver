<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Boolean;

final class False_ extends Boolean implements PseudoType
{
    public function underlyingType(): Type
    {
        return new Boolean();
    }

    public function __toString(): string
    {
        return 'false';
    }
}