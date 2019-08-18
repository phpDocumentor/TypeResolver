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

namespace phpDocumentor\Reflection\Types;

use UnexpectedValueException;

/**
 * Convenience class to create a Context for DocBlocks when not using the Reflection Component of phpDocumentor.
 *
 * For a DocBlock to be able to resolve types that use partial namespace names or rely on namespace imports we need to
 * provide a bit of context so that the DocBlock can read that and based on it decide how to resolve the types to
 * Fully Qualified names.
 *
 * @see Context for more information.
 */
final class ContextFactory
{
    /** The literal used at the end of a use statement. */
    const T_LITERAL_END_OF_USE = ';';

    /** The literal used between sets of use statements */
    const T_LITERAL_USE_SEPARATOR = ',';

    /**
     * Build a Context given a Class Reflection.
     *
     * @see Context for more information on Contexts.
     */
    public function createFromReflector(\Reflector $reflector): Context
    {
        if ($reflector instanceof \ReflectionClass) {
            return $this->createFromReflectionClass($reflector);
        }

        if ($reflector instanceof \ReflectionParameter) {
            return $this->createFromReflectionParameter($reflector);
        }

        if ($reflector instanceof \ReflectionMethod) {
            return $this->createFromReflectionMethod($reflector);
        }

        if ($reflector instanceof \ReflectionProperty) {
            return $this->createFromReflectionProperty($reflector);
        }

        if ($reflector instanceof \ReflectionClassConstant) {
            return $this->createFromReflectionClassConstant($reflector);
        }

        throw new UnexpectedValueException('Unhandled \Reflector instance given:  ' . get_class($reflector));
    }

    private function createFromReflectionParameter(\ReflectionParameter $parameter): Context
    {
        $class = $parameter->getDeclaringClass();
        if ($class) {
            return $this->createFromReflectionClass($class);
        }
    }

    private function createFromReflectionMethod(\ReflectionMethod $method): Context
    {
        return $this->createFromReflectionClass($method->getDeclaringClass());
    }

    private function createFromReflectionProperty(\ReflectionProperty $property): Context
    {
        return $this->createFromReflectionClass($property->getDeclaringClass());
    }

    private function createFromReflectionClassConstant(\ReflectionClassConstant $constant): Context
    {
        return $this->createFromReflectionClass($constant->getDeclaringClass());
    }

    private function createFromReflectionClass(\ReflectionClass $class): Context
    {
        $fileName = $class->getFileName();
        $namespace = $class->getNamespaceName();

        if (is_string($fileName) && file_exists($fileName)) {
            $contents = file_get_contents($fileName);
            if (false === $contents) {
                throw new \RuntimeException('Unable to read file "' . $fileName . '"');
            }

            return $this->createForNamespace($namespace, $contents);
        }

        return new Context($namespace, []);
    }

    /**
     * Build a Context for a namespace in the provided file contents.
     *
     * @param string $namespace It does not matter if a `\` precedes the namespace name, this method first normalizes.
     * @param string $fileContents the file's contents to retrieve the aliases from with the given namespace.
     *
     * @see Context for more information on Contexts.
     *
     * @return Context
     */
    public function createForNamespace($namespace, $fileContents)
    {
        $namespace = trim($namespace, '\\');
        $useStatements = [];
        $currentNamespace = '';
        $tokens = new \ArrayIterator(token_get_all($fileContents));

        while ($tokens->valid()) {
            switch ($tokens->current()[0]) {
                case T_NAMESPACE:
                    $currentNamespace = $this->parseNamespace($tokens);
                    break;
                case T_CLASS:
                    // Fast-forward the iterator through the class so that any
                    // T_USE tokens found within are skipped - these are not
                    // valid namespace use statements so should be ignored.
                    $braceLevel = 0;
                    $firstBraceFound = false;
                    while ($tokens->valid() && ($braceLevel > 0 || !$firstBraceFound)) {
                        if ($tokens->current() === '{'
                            || $tokens->current()[0] === T_CURLY_OPEN
                            || $tokens->current()[0] === T_DOLLAR_OPEN_CURLY_BRACES) {
                            if (!$firstBraceFound) {
                                $firstBraceFound = true;
                            }

                            ++$braceLevel;
                        }

                        if ($tokens->current() === '}') {
                            --$braceLevel;
                        }

                        $tokens->next();
                    }
                    break;
                case T_USE:
                    if ($currentNamespace === $namespace) {
                        $useStatements = array_merge($useStatements, $this->parseUseStatement($tokens));
                    }
                    break;
            }

            $tokens->next();
        }

        return new Context($namespace, $useStatements);
    }

    /**
     * Deduce the name from tokens when we are at the T_NAMESPACE token.
     *
     * @return string
     */
    private function parseNamespace(\ArrayIterator $tokens)
    {
        // skip to the first string or namespace separator
        $this->skipToNextStringOrNamespaceSeparator($tokens);

        $name = '';
        while ($tokens->valid() && ($tokens->current()[0] === T_STRING || $tokens->current()[0] === T_NS_SEPARATOR)
        ) {
            $name .= $tokens->current()[1];
            $tokens->next();
        }

        return $name;
    }

    /**
     * Deduce the names of all imports when we are at the T_USE token.
     *
     * @return string[]
     */
    private function parseUseStatement(\ArrayIterator $tokens)
    {
        $uses = [];
        $continue = true;

        while ($continue) {
            $this->skipToNextStringOrNamespaceSeparator($tokens);

            $uses = array_merge($uses, $this->extractUseStatements($tokens));
            if ($tokens->current()[0] === self::T_LITERAL_END_OF_USE) {
                $continue = false;
            }
        }

        return $uses;
    }

    /**
     * Fast-forwards the iterator as longs as we don't encounter a T_STRING or T_NS_SEPARATOR token.
     */
    private function skipToNextStringOrNamespaceSeparator(\ArrayIterator $tokens)
    {
        while ($tokens->valid() && ($tokens->current()[0] !== T_STRING) && ($tokens->current()[0] !== T_NS_SEPARATOR)) {
            $tokens->next();
        }
    }

    /**
     * Deduce the namespace name and alias of an import when we are at the T_USE token or have not reached the end of
     * a USE statement yet. This will return a key/value array of the alias => namespace.
     *
     * @return array
     */
    private function extractUseStatements(\ArrayIterator $tokens)
    {
        $extractedUseStatements = [];
        $groupedNs              = '';
        $currentNs              = '';
        $currentAlias           = '';
        $state                  = 'start';

        $i = 0;
        while ($tokens->valid()) {
            $i += 1;
            $currentToken = $tokens->current();
            $tokenId = is_string($currentToken) ? $currentToken : $currentToken[0];
            $tokenValue = is_string($currentToken) ? null : $currentToken[1];
            switch ($state) {
                case 'start':
                    switch ($tokenId) {
                        case T_STRING:
                        case T_NS_SEPARATOR:
                            $currentNs .= $tokenValue;
                            $currentAlias = $tokenValue;
                            break;
                        case T_CURLY_OPEN:
                        case '{':
                            $state = 'grouped';
                            $groupedNs = $currentNs;
                            break;
                        case T_AS:
                            $state = 'start-alias';
                            break;
                        case self::T_LITERAL_USE_SEPARATOR:
                        case self::T_LITERAL_END_OF_USE:
                            $state = 'end';
                            break;
                        default:
                            break;
                    }
                    break;
                case 'start-alias':
                    switch ($tokenId) {
                        case T_STRING:
                            $currentAlias = $tokenValue;
                            break;
                        case self::T_LITERAL_USE_SEPARATOR:
                        case self::T_LITERAL_END_OF_USE:
                            $state = 'end';
                            break;
                        default:
                            break;
                    }
                    break;
                case 'grouped':
                    switch ($tokenId) {
                        case T_STRING:
                        case T_NS_SEPARATOR:
                            $currentNs .= $tokenValue;
                            $currentAlias = $tokenValue;
                            break;
                        case T_AS:
                            $state = 'grouped-alias';
                            break;
                        case self::T_LITERAL_USE_SEPARATOR:
                            $state                                 = 'grouped';
                            $extractedUseStatements[$currentAlias] = $currentNs;
                            $currentNs                             = $groupedNs;
                            $currentAlias                          = '';
                            break;
                        case self::T_LITERAL_END_OF_USE:
                            $state = 'end';
                            break;
                        default:
                            break;
                    }
                    break;
                case 'grouped-alias':
                    switch ($tokenId) {
                        case T_STRING:
                            $currentAlias = $tokenValue;
                            break;
                        case self::T_LITERAL_USE_SEPARATOR:
                            $state                                 = 'grouped';
                            $extractedUseStatements[$currentAlias] = $currentNs;
                            $currentNs                             = $groupedNs;
                            $currentAlias                          = '';
                            break;
                        case self::T_LITERAL_END_OF_USE:
                            $state = 'end';
                            break;
                        default:
                            break;
                    }
            }

            if ($state === 'end') {
                break;
            }

            $tokens->next();
        }

        if ($groupedNs !== $currentNs) {
            $extractedUseStatements[$currentAlias] = $currentNs;
        }

        return $extractedUseStatements;
    }
}
