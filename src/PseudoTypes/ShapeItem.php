<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Mixed_;

use function sprintf;

abstract class ShapeItem
{
    /** @var string|null */
    private $key;
    /** @var Type */
    private $value;
    /** @var bool */
    private $optional;

    public function __construct(?string $key, ?Type $value, bool $optional)
    {
        $this->key = $key;
        $this->value = $value ?? new Mixed_();
        $this->optional = $optional;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getValue(): Type
    {
        return $this->value;
    }

    public function isOptional(): bool
    {
        return $this->optional;
    }

    public function __toString(): string
    {
        if ($this->key !== null) {
            return sprintf(
                '%s%s: %s',
                $this->key,
                $this->optional ? '?' : '',
                (string) $this->value
            );
        }

        return (string) $this->value;
    }
}
