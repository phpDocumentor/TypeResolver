<?php declare(strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

use phpDocumentor\Reflection\Types\Context;

class FqsenResolver
{
    /** @var string Definition of the NAMESPACE operator in PHP */
    const OPERATOR_NAMESPACE = '\\';

    public function resolve($fqsen, Context $context = null)
    {
        if ($context === null) {
            $context = new Context('');
        }

        if ($this->isFqsen($fqsen)) {
            return new Fqsen($fqsen);
        }

        return $this->resolvePartialStructuralElementName($fqsen, $context);
    }

    /**
     * Tests whether the given type is a Fully Qualified Structural Element Name.
     *
     * @param string $type
     *
     * @return bool
     */
    private function isFqsen($type)
    {
        return strpos($type, self::OPERATOR_NAMESPACE) === 0;
    }

    /**
     * Resolves a partial Structural Element Name (i.e. `Reflection\DocBlock`) to its FQSEN representation
     * (i.e. `\phpDocumentor\Reflection\DocBlock`) based on the Namespace and aliases mentioned in the Context.
     *
     * @param string $type
     * @return Fqsen
     * @throws \InvalidArgumentException when type is not a valid FQSEN.
     */
    private function resolvePartialStructuralElementName($type, Context $context)
    {
        $typeParts = explode(self::OPERATOR_NAMESPACE, $type, 2);

        $namespaceAliases = $context->getNamespaceAliases();

        // if the first segment is not an alias; prepend namespace name and return
        if (!isset($namespaceAliases[$typeParts[0]])) {
            $namespace = $context->getNamespace();

            // check if class is not a part of the current context
            foreach ($namespaceAliases as $value) {
                // if it is a part of current context - leave foreach
                if (strpos($value, $namespace) !== false) {
                    break;
                }

                // check case if typename is a part of namespace, and alias has another part
                $_typeParts = explode(self::OPERATOR_NAMESPACE, $type);
                if (count($_typeParts) > 2) {
                    $_type = $_typeParts[0];
                    if (strrpos($value, $_type) === strlen($value) - strlen($_type))
                    {
                        unset($_typeParts[0]);
                        return new Fqsen(self::OPERATOR_NAMESPACE . $value . self::OPERATOR_NAMESPACE . implode(self::OPERATOR_NAMESPACE, $_typeParts));
                    }
                }

                // if found alias
                if (strrpos($value, $type) === strlen($value) - strlen($type)) {
                    return new Fqsen(self::OPERATOR_NAMESPACE . $value);
                }
            }

            if ('' !== $namespace) {
                $namespace .= self::OPERATOR_NAMESPACE;
            }

            return new Fqsen(self::OPERATOR_NAMESPACE . $namespace . $type);
        }

        $typeParts[0] = $namespaceAliases[$typeParts[0]];

        return new Fqsen(self::OPERATOR_NAMESPACE . implode(self::OPERATOR_NAMESPACE, $typeParts));
    }
}
