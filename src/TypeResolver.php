<?php declare(strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Integer;

final class TypeResolver
{
    /** @var string Definition of the ARRAY operator for types */
    const OPERATOR_ARRAY = '[]';

    /** @var string Definition of the NAMESPACE operator in PHP */
    const OPERATOR_NAMESPACE = '\\';

    /** @var integer the iterator parser is inside a compound context */
    const PARSER_IN_COMPOUND = 0;

    /** @var integer the iterator parser is inside a nullable expression context */
    const PARSER_IN_NULLABLE = 1;

    /** @var integer the iterator parser is inside an array expression context */
    const PARSER_IN_ARRAY_EXPRESSION = 2;

    /** @var integer the iterator parser is inside a collection expression context */
    const PARSER_IN_COLLECTION_EXPRESSION = 3;


    /** @var string[] List of recognized keywords and unto which Value Object they map */
    private $keywords = array(
        'string' => Types\String_::class,
        'int' => Types\Integer::class,
        'integer' => Types\Integer::class,
        'bool' => Types\Boolean::class,
        'boolean' => Types\Boolean::class,
        'real' => Types\Float_::class,
        'float' => Types\Float_::class,
        'double' => Types\Float_::class,
        'object' => Object_::class,
        'mixed' => Types\Mixed_::class,
        'array' => Array_::class,
        'resource' => Types\Resource_::class,
        'void' => Types\Void_::class,
        'null' => Types\Null_::class,
        'scalar' => Types\Scalar::class,
        'callback' => Types\Callable_::class,
        'callable' => Types\Callable_::class,
        'false' => Types\Boolean::class,
        'true' => Types\Boolean::class,
        'self' => Types\Self_::class,
        '$this' => Types\This::class,
        'static' => Types\Static_::class,
        'parent' => Types\Parent_::class,
        'iterable' => Iterable_::class,
    );

    /** @var FqsenResolver */
    private $fqsenResolver;

    /**
     * Initializes this TypeResolver with the means to create and resolve Fqsen objects.
     *
     * @param FqsenResolver $fqsenResolver
     */
    public function __construct(FqsenResolver $fqsenResolver = null)
    {
        $this->fqsenResolver = $fqsenResolver ?: new FqsenResolver();
    }

    /**
     * Analyzes the given type and returns the FQCN variant.
     *
     * When a type is provided this method checks whether it is not a keyword or
     * Fully Qualified Class Name. If so it will use the given namespace and
     * aliases to expand the type to a FQCN representation.
     *
     * This method only works as expected if the namespace and aliases are set;
     * no dynamic reflection is being performed here.
     *
     * @param string $type     The relative or absolute type.
     * @param Context $context
     *
     * @uses Context::getNamespace()        to determine with what to prefix the type name.
     * @uses Context::getNamespaceAliases() to check whether the first part of the relative type name should not be
     *     replaced with another namespace.
     *
     * @return Type
     */
    public function resolve($type, Context $context = null)
    {
        if (!is_string($type)) {
            throw new \InvalidArgumentException(
                'Attempted to resolve type but it appeared not to be a string, received: ' . var_export($type, true)
            );
        }

        $type = trim($type);
        if (!$type) {
            throw new \InvalidArgumentException('Attempted to resolve "' . $type . '" but it appears to be empty');
        }

        if ($context === null) {
            $context = new Context('');
        }

        // split the type string into tokens `|`, `?`, `(`, `)[]`, '<', '>' and type names
        $tokens = preg_split('/(\\||\\?|<|>|,|\\(|\\)(?:\\[\\])+)/', $type, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $tokenIterator = new \ArrayIterator($tokens);

        return $this->parseTypes($tokenIterator, $context, self::PARSER_IN_COMPOUND);
    }

    /**
     * Analyse each tokens and creates types
     *
     * @param \ArrayIterator $tokens  the iterator on tokens
     * @param Context        $context
     * @param integer        $parserContext on of self::PARSER_* constants, indicating
     *                        the context where we are in the parsing
     *
     * @return Type
     */
    private function parseTypes(\ArrayIterator $tokens, Context $context, $parserContext)
    {
        $types = array();
        $token = '';
        while ($tokens->valid()) {
            $token = $tokens->current();

            if ($token == '|') {
                if (count($types) == 0) {
                    throw new \RuntimeException(
                        'A type is missing before a type separator'
                    );
                }
                if ($parserContext !== self::PARSER_IN_COMPOUND
                    && $parserContext !== self::PARSER_IN_ARRAY_EXPRESSION
                    && $parserContext !== self::PARSER_IN_COLLECTION_EXPRESSION
                ) {
                    throw new \RuntimeException(
                        'Unexpected type separator'
                    );
                }
                $tokens->next();

            } else if ($token == '?') {
                if ($parserContext !== self::PARSER_IN_COMPOUND
                    && $parserContext !== self::PARSER_IN_ARRAY_EXPRESSION
                    && $parserContext !== self::PARSER_IN_COLLECTION_EXPRESSION
                ) {
                    throw new \RuntimeException(
                        'Unexpected nullable character'
                    );
                }

                $tokens->next();
                $type = $this->parseTypes($tokens, $context, self::PARSER_IN_NULLABLE);
                $types[] = new Nullable($type);

            } else if ($token === '(') {
                $tokens->next();
                $type = $this->parseTypes($tokens, $context, self::PARSER_IN_ARRAY_EXPRESSION);

                $resolvedType = new Array_($type);

                // we generates arrays corresponding to the number of '[]'
                // after the ')'
                $numberOfArrays = (strlen($tokens->current()) -1) / 2;
                for ($i = 0; $i < $numberOfArrays - 1; $i++) {
                    $resolvedType = new Array_($resolvedType);
                }
                $types[] = $resolvedType;
                $tokens->next();

            } else if ($parserContext === self::PARSER_IN_ARRAY_EXPRESSION
                       && $token[0] === ')'
                ) {
                break;

            } else if ($token === '<') {
                if (count($types) === 0) {
                    throw new \RuntimeException(
                        'Unexpected collection operator "<", class name is missing'
                    );
                }
                $classType = array_pop($types);

                $types[] = $this->resolveCollection($tokens, $classType, $context);

                $tokens->next();

            } else if ($parserContext === self::PARSER_IN_COLLECTION_EXPRESSION
                && ($token === '>' || $token === ',')
                ) {
                break;
            } else {
                $type = $this->resolveSingleType($token, $context);
                $tokens->next();
                if ($parserContext === self::PARSER_IN_NULLABLE) {
                    return $type;
                }
                $types[] = $type;
            }
        }

        if ($token == '|') {
            throw new \RuntimeException(
                'A type is missing after a type separator'
            );
        }

        if (count($types) == 0) {
            if ($parserContext == self::PARSER_IN_NULLABLE) {
                throw new \RuntimeException(
                    'A type is missing after a nullable character'
                );
            }
            if ($parserContext == self::PARSER_IN_ARRAY_EXPRESSION) {
                throw new \RuntimeException(
                    'A type is missing in an array expression'
                );
            }
            if ($parserContext == self::PARSER_IN_COLLECTION_EXPRESSION) {
                throw new \RuntimeException(
                    'A type is missing in a collection expression'
                );
            }
            throw new \RuntimeException(
                'No types in a compound list'
            );
        } else if (count($types) == 1) {
            return $types[0];
        }
        return new Compound($types);
    }

    /**
     * resolve the given type into a type object
     *
     * @param string    $type      the type string, representing a single type
     * @param Context   $context
     * @return Type|Array_|Object_
     */
    private function resolveSingleType($type, Context $context)
    {
        switch (true) {
            case $this->isKeyword($type):
                return $this->resolveKeyword($type);
            case $this->isTypedArray($type):
                return $this->resolveTypedArray($type, $context);
            case $this->isFqsen($type):
                return $this->resolveTypedObject($type);
            case $this->isPartialStructuralElementName($type):
                return $this->resolveTypedObject($type, $context);
            // @codeCoverageIgnoreStart
            default:
                // I haven't got the foggiest how the logic would come here but added this as a defense.
                throw new \RuntimeException(
                    'Unable to resolve type "' . $type . '", there is no known method to resolve it'
                );
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Adds a keyword to the list of Keywords and associates it with a specific Value Object.
     *
     * @param string $keyword
     * @param string $typeClassName
     *
     * @return void
     */
    public function addKeyword($keyword, $typeClassName)
    {
        if (!class_exists($typeClassName)) {
            throw new \InvalidArgumentException(
                'The Value Object that needs to be created with a keyword "' . $keyword . '" must be an existing class'
                . ' but we could not find the class ' . $typeClassName
            );
        }

        if (!in_array(Type::class, class_implements($typeClassName))) {
            throw new \InvalidArgumentException(
                'The class "' . $typeClassName . '" must implement the interface "phpDocumentor\Reflection\Type"'
            );
        }

        $this->keywords[$keyword] = $typeClassName;
    }

    /**
     * Detects whether the given type represents an array.
     *
     * @param string $type A relative or absolute type as defined in the phpDocumentor documentation.
     *
     * @return bool
     */
    private function isTypedArray($type)
    {
        return substr($type, -2) === self::OPERATOR_ARRAY;
    }

    /**
     * Detects whether the given type represents a PHPDoc keyword.
     *
     * @param string $type A relative or absolute type as defined in the phpDocumentor documentation.
     *
     * @return bool
     */
    private function isKeyword($type)
    {
        return in_array(strtolower($type), array_keys($this->keywords), true);
    }

    /**
     * Detects whether the given type represents a relative structural element name.
     *
     * @param string $type A relative or absolute type as defined in the phpDocumentor documentation.
     *
     * @return bool
     */
    private function isPartialStructuralElementName($type)
    {
        return ($type[0] !== self::OPERATOR_NAMESPACE) && !$this->isKeyword($type);
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
     * Resolves the given typed array string (i.e. `string[]`) into an Array object with the right types set.
     *
     * @param string $type
     * @param Context $context
     *
     * @return Array_
     */
    private function resolveTypedArray($type, Context $context)
    {
        return new Array_($this->resolveSingleType(substr($type, 0, -2), $context));
    }

    /**
     * Resolves the given keyword (such as `string`) into a Type object representing that keyword.
     *
     * @param string $type
     *
     * @return Type
     */
    private function resolveKeyword($type)
    {
        $className = $this->keywords[strtolower($type)];

        return new $className();
    }

    /**
     * Resolves the given FQSEN string into an FQSEN object.
     *
     * @param string $type
     * @param Context|null $context
     *
     * @return Object_
     */
    private function resolveTypedObject($type, Context $context = null)
    {
        return new Object_($this->fqsenResolver->resolve($type, $context));
    }

    /**
     * Resolves the collection values and keys
     *
     * @param \ArrayIterator $tokens
     * @param Type $classType
     * @param Context|null $context
     * @return Array_|Collection
     */
    private function resolveCollection(\ArrayIterator $tokens, Type $classType, Context $context = null) {

        $isArray = ('array' == (string) $classType);

        // allow only "array" or class name before "<"
        if (!$isArray
            && (! $classType instanceof Object_ || $classType->getFqsen() === null)) {
            throw new \RuntimeException(
                $classType.' is not a collection'
            );
        }

        $tokens->next();

        $valueType = $this->parseTypes($tokens, $context, self::PARSER_IN_COLLECTION_EXPRESSION);
        $keyType = null;

        if ($tokens->current() == ',') {
            // if we have a coma, then we just parsed the key type, not the value type
            $keyType = $valueType;
            if ($isArray) {
                // check the key type for an "array" collection. We allow only
                // strings or integers.
                if (! $keyType instanceof String_ &&
                    ! $keyType instanceof Integer &&
                    ! $keyType instanceof Compound
                ) {
                    throw new \RuntimeException(
                        'An array can have only integers or strings as keys'
                    );
                }
                if ($keyType instanceof Compound) {
                    foreach($keyType->getIterator() as $item) {
                        if (! $item instanceof String_ &&
                            ! $item instanceof Integer
                        ) {
                            throw new \RuntimeException(
                                'An array can have only integers or strings as keys'
                            );
                        }
                    }
                }
            }
            $tokens->next();
            // now let's parse the value type
            $valueType = $this->parseTypes($tokens, $context, self::PARSER_IN_COLLECTION_EXPRESSION);
        }

        if ($tokens->current() !== '>') {
            if ($tokens->current() == '') {
                throw new \RuntimeException(
                    'Collection: ">" is missing'
                );
            }

            throw new \RuntimeException(
                'Unexpected character "'.$tokens->current().'", ">" is missing'
            );
        }
        if ($isArray) {
            return new Array_($valueType, $keyType);
        }
        else {
            return new Collection($classType->getFqsen(), $valueType, $keyType);
        }
    }
}
