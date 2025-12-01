<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Type;

use function implode;

/**
 * Value Object representing a Callable type.
 *
 * @psalm-immutable
 */
final class Callable_ implements Type
{
    /** @var string */
    private $identifier;
    /** @var Type|null */
    private $returnType;
    /** @var CallableParameter[] */
    private $parameters;

    /**
     * @param CallableParameter[] $parameters
     */
    public function __construct(
        string $identifier = 'callable',
        array $parameters = [],
        ?Type $returnType = null
    ) {
        $this->identifier = $identifier;
        $this->parameters = $parameters;
        $this->returnType = $returnType;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /** @return CallableParameter[] */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getReturnType(): ?Type
    {
        return $this->returnType;
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString(): string
    {
        if (!$this->parameters && $this->returnType === null) {
            return $this->identifier;
        }

        if ($this->returnType instanceof self) {
            $returnType = '(' . (string) $this->returnType . ')';
        } else {
            $returnType = (string) $this->returnType;
        }

        return $this->identifier . '(' . implode(', ', $this->parameters) . '): ' . $returnType;
    }
}
