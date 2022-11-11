<?php
/*
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @link      http://phpdoc.org
 *
 */

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Mixed_;

use function sprintf;

/** @psalm-immutable */
final class ConstExpression implements PseudoType
{
    private Fqsen $owner;
    private string $expression;

    public function __construct(Fqsen $owner, string $expression)
    {
        $this->owner = $owner;
        $this->expression = $expression;
    }

    public function getOwner(): Fqsen
    {
        return $this->owner;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function underlyingType(): Type
    {
        return new Mixed_();
    }

    public function __toString(): string
    {
        return sprintf('%s::%s', (string) $this->owner, $this->expression);
    }
}
