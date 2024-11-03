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

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Type;

use function sprintf;

/** @psalm-immutable */
final class ConditionalParameter extends AbstractConditional
{
    /** @var string */
    private $parameter;

    public function __construct(string $parameter, Type $target, Type $if, Type $else, bool $negated)
    {
        parent::__construct($target, $if, $else, $negated);
        $this->parameter = $parameter;
    }

    public function getParameterName(): string
    {
        return $this->parameter;
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString(): string
    {
        return sprintf(
            '(%s %s %s ? %s : %s)',
            $this->parameter,
            $this->negated ? 'is not' : 'is',
            $this->target,
            $this->if,
            $this->else
        );
    }
}
