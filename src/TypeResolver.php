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

namespace phpDocumentor\Reflection;

use ArrayIterator;
use InvalidArgumentException;
use phpDocumentor\Reflection\PseudoTypes\IntegerRange;
use phpDocumentor\Reflection\PseudoTypes\List_;
use phpDocumentor\Reflection\Types\AggregatedType;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\ArrayKey;
use phpDocumentor\Reflection\Types\ClassString;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Expression;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\InterfaceString;
use phpDocumentor\Reflection\Types\Intersection;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use RuntimeException;

use function array_key_exists;
use function array_pop;
use function array_values;
use function assert;
use function class_exists;
use function class_implements;
use function count;
use function current;
use function end;
use function in_array;
use function is_numeric;
use function key;
use function preg_split;
use function strpos;
use function strtolower;
use function trim;

use const PREG_SPLIT_DELIM_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;

final class TypeResolver
{
    /** @var string Definition of the ARRAY operator for types */
    private const OPERATOR_ARRAY = '[]';

    /** @var string Definition of the NAMESPACE operator in PHP */
    private const OPERATOR_NAMESPACE = '\\';

    /** @var int the iterator parser is inside a compound context */
    private const PARSER_IN_COMPOUND = 0;

    /** @var int the iterator parser is inside a nullable expression context */
    private const PARSER_IN_NULLABLE = 1;

    /** @var int the iterator parser is inside an array expression context */
    private const PARSER_IN_ARRAY_EXPRESSION = 2;

    /** @var int the iterator parser is inside a collection expression context */
    private const PARSER_IN_COLLECTION_EXPRESSION = 3;

    /**
     * @var array<string, string> List of recognized keywords and unto which Value Object they map
     * @psalm-var array<string, class-string<Type>>
     */
    private $keywords = [
        'string' => Types\String_::class,
        'class-string' => Types\ClassString::class,
        'interface-string' => Types\InterfaceString::class,
        'html-escaped-string' => PseudoTypes\HtmlEscapedString::class,
        'lowercase-string' => PseudoTypes\LowercaseString::class,
        'non-empty-lowercase-string' => PseudoTypes\NonEmptyLowercaseString::class,
        'non-empty-string' => PseudoTypes\NonEmptyString::class,
        'numeric-string' => PseudoTypes\NumericString::class,
        'numeric' => PseudoTypes\Numeric_::class,
        'trait-string' => PseudoTypes\TraitString::class,
        'int' => Types\Integer::class,
        'integer' => Types\Integer::class,
        'positive-int' => PseudoTypes\PositiveInteger::class,
        'negative-int' => PseudoTypes\NegativeInteger::class,
        'bool' => Types\Boolean::class,
        'boolean' => Types\Boolean::class,
        'real' => Types\Float_::class,
        'float' => Types\Float_::class,
        'double' => Types\Float_::class,
        'object' => Types\Object_::class,
        'mixed' => Types\Mixed_::class,
        'array' => Types\Array_::class,
        'array-key' => Types\ArrayKey::class,
        'resource' => Types\Resource_::class,
        'void' => Types\Void_::class,
        'null' => Types\Null_::class,
        'scalar' => Types\Scalar::class,
        'callback' => Types\Callable_::class,
        'callable' => Types\Callable_::class,
        'callable-string' => PseudoTypes\CallableString::class,
        'false' => PseudoTypes\False_::class,
        'true' => PseudoTypes\True_::class,
        'literal-string' => PseudoTypes\LiteralString::class,
        'self' => Types\Self_::class,
        '$this' => Types\This::class,
        'static' => Types\Static_::class,
        'parent' => Types\Parent_::class,
        'iterable' => Types\Iterable_::class,
        'never' => Types\Never_::class,
        'list' => PseudoTypes\List_::class,
    ];

    /**
     * @var FqsenResolver
     * @psalm-readonly
     */
    private $fqsenResolver;

    /**
     * Initializes this TypeResolver with the means to create and resolve Fqsen objects.
     */
    public function __construct(?FqsenResolver $fqsenResolver = null)
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
     * @uses Context::getNamespaceAliases() to check whether the first part of the relative type name should not be
     * replaced with another namespace.
     * @uses Context::getNamespace()        to determine with what to prefix the type name.
     *
     * @param string $type The relative or absolute type.
     */
    public function resolve(string $type, ?Context $context = null): Type
    {
        $type = trim($type);
        if (!$type) {
            throw new InvalidArgumentException('Attempted to resolve "' . $type . '" but it appears to be empty');
        }

        if ($context === null) {
            $context = new Context('');
        }

        // split the type string into tokens `|`, `?`, `<`, `>`, `,`, `(`, `)`, `[]`, '<', '>' and type names
        $tokens = preg_split(
            '/(\\||\\?|<|>|&|, ?|\\(|\\)|\\[\\]+)/',
            $type,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        if ($tokens === false) {
            throw new InvalidArgumentException('Unable to split the type string "' . $type . '" into tokens');
        }

        /** @var ArrayIterator<int, string|null> $tokenIterator */
        $tokenIterator = new ArrayIterator($tokens);

        return $this->parseTypes($tokenIterator, $context, self::PARSER_IN_COMPOUND);
    }

    /**
     * Analyse each tokens and creates types
     *
     * @param ArrayIterator<int, string|null> $tokens        the iterator on tokens
     * @param int                        $parserContext on of self::PARSER_* constants, indicating
     * the context where we are in the parsing
     */
    private function parseTypes(ArrayIterator $tokens, Context $context, int $parserContext): Type
    {
        $types = [];
        $token = '';
        $compoundToken = '|';
        while ($tokens->valid()) {
            $token = $tokens->current();
            if ($token === null) {
                throw new RuntimeException(
                    'Unexpected nullable character'
                );
            }

            if ($token === '|' || $token === '&') {
                if (count($types) === 0) {
                    throw new RuntimeException(
                        'A type is missing before a type separator'
                    );
                }

                if (
                    !in_array($parserContext, [
                        self::PARSER_IN_COMPOUND,
                        self::PARSER_IN_ARRAY_EXPRESSION,
                        self::PARSER_IN_COLLECTION_EXPRESSION,
                    ], true)
                ) {
                    throw new RuntimeException(
                        'Unexpected type separator'
                    );
                }

                $compoundToken = $token;
                $tokens->next();
            } elseif ($token === '?') {
                if (
                    !in_array($parserContext, [
                        self::PARSER_IN_COMPOUND,
                        self::PARSER_IN_ARRAY_EXPRESSION,
                        self::PARSER_IN_COLLECTION_EXPRESSION,
                    ], true)
                ) {
                    throw new RuntimeException(
                        'Unexpected nullable character'
                    );
                }

                $tokens->next();
                $type    = $this->parseTypes($tokens, $context, self::PARSER_IN_NULLABLE);
                $types[] = new Nullable($type);
            } elseif ($token === '(') {
                $tokens->next();
                $type = $this->parseTypes($tokens, $context, self::PARSER_IN_ARRAY_EXPRESSION);

                $token = $tokens->current();
                if ($token === null) { // Someone did not properly close their array expression ..
                    break;
                }

                $tokens->next();

                $resolvedType = new Expression($type);

                $types[] = $resolvedType;
            } elseif ($parserContext === self::PARSER_IN_ARRAY_EXPRESSION && isset($token[0]) && $token[0] === ')') {
                break;
            } elseif ($token === '<') {
                if (count($types) === 0) {
                    throw new RuntimeException(
                        'Unexpected collection operator "<", class name is missing'
                    );
                }

                $classType = array_pop($types);
                if ($classType !== null) {
                    if ((string) $classType === 'class-string') {
                        foreach ($this->resolveClassString($tokens, $context) as $classStringType) {
                            $types[] = $classStringType;
                        }
                    } elseif ((string) $classType === 'int') {
                        $types[] = $this->resolveIntRange($tokens);
                    } elseif ((string) $classType === 'interface-string') {
                        $types[] = $this->resolveInterfaceString($tokens, $context);
                    } else {
                        $types[] = $this->resolveCollection($tokens, $classType, $context);
                    }
                }

                $tokens->next();
            } elseif (
                $parserContext === self::PARSER_IN_COLLECTION_EXPRESSION
                && ($token === '>' || trim($token) === ',')
            ) {
                break;
            } elseif ($token === self::OPERATOR_ARRAY) {
                end($types);
                $last = key($types);
                if ($last === null) {
                    throw new InvalidArgumentException('Unexpected array operator');
                }

                $lastItem = $types[$last];
                if ($lastItem instanceof Expression) {
                    $lastItem = $lastItem->getValueType();
                }

                $types[$last] = new Array_($lastItem);

                $tokens->next();
            } else {
                $type = $this->resolveSingleType($token, $context);
                $tokens->next();
                if ($parserContext === self::PARSER_IN_NULLABLE) {
                    return $type;
                }

                $types[] = $type;
            }
        }

        if ($token === '|' || $token === '&') {
            throw new RuntimeException(
                'A type is missing after a type separator'
            );
        }

        if (count($types) === 0) {
            if ($parserContext === self::PARSER_IN_NULLABLE) {
                throw new RuntimeException(
                    'A type is missing after a nullable character'
                );
            }

            if ($parserContext === self::PARSER_IN_ARRAY_EXPRESSION) {
                throw new RuntimeException(
                    'A type is missing in an array expression'
                );
            }

            if ($parserContext === self::PARSER_IN_COLLECTION_EXPRESSION) {
                throw new RuntimeException(
                    'A type is missing in a collection expression'
                );
            }
        } elseif (count($types) === 1) {
            return current($types);
        }

        if ($compoundToken === '|') {
            return new Compound(array_values($types));
        }

        return new Intersection(array_values($types));
    }

    /**
     * resolve the given type into a type object
     *
     * @param string $type the type string, representing a single type
     *
     * @return Type|Array_|Object_
     *
     * @psalm-mutation-free
     */
    private function resolveSingleType(string $type, Context $context): object
    {
        switch (true) {
            case $this->isKeyword($type):
                return $this->resolveKeyword($type);

            case $this->isFqsen($type):
                return $this->resolveTypedObject($type);

            case $this->isPartialStructuralElementName($type):
                return $this->resolveTypedObject($type, $context);

            // @codeCoverageIgnoreStart
            default:
                // I haven't got the foggiest how the logic would come here but added this as a defense.
                throw new RuntimeException(
                    'Unable to resolve type "' . $type . '", there is no known method to resolve it'
                );
        }

        // @codeCoverageIgnoreEnd
    }

    /**
     * Adds a keyword to the list of Keywords and associates it with a specific Value Object.
     *
     * @psalm-param class-string<Type> $typeClassName
     */
    public function addKeyword(string $keyword, string $typeClassName): void
    {
        if (!class_exists($typeClassName)) {
            throw new InvalidArgumentException(
                'The Value Object that needs to be created with a keyword "' . $keyword . '" must be an existing class'
                . ' but we could not find the class ' . $typeClassName
            );
        }

        $interfaces = class_implements($typeClassName);
        if ($interfaces === false) {
            throw new InvalidArgumentException(
                'The Value Object that needs to be created with a keyword "' . $keyword . '" must be an existing class'
                . ' but we could not find the class ' . $typeClassName
            );
        }

        if (!in_array(Type::class, $interfaces, true)) {
            throw new InvalidArgumentException(
                'The class "' . $typeClassName . '" must implement the interface "phpDocumentor\Reflection\Type"'
            );
        }

        $this->keywords[$keyword] = $typeClassName;
    }

    /**
     * Detects whether the given type represents a PHPDoc keyword.
     *
     * @param string $type A relative or absolute type as defined in the phpDocumentor documentation.
     *
     * @psalm-mutation-free
     */
    private function isKeyword(string $type): bool
    {
        return array_key_exists(strtolower($type), $this->keywords);
    }

    /**
     * Detects whether the given type represents a relative structural element name.
     *
     * @param string $type A relative or absolute type as defined in the phpDocumentor documentation.
     *
     * @psalm-mutation-free
     */
    private function isPartialStructuralElementName(string $type): bool
    {
        return (isset($type[0]) && $type[0] !== self::OPERATOR_NAMESPACE) && !$this->isKeyword($type);
    }

    /**
     * Tests whether the given type is a Fully Qualified Structural Element Name.
     *
     * @psalm-mutation-free
     */
    private function isFqsen(string $type): bool
    {
        return strpos($type, self::OPERATOR_NAMESPACE) === 0;
    }

    /**
     * Resolves the given keyword (such as `string`) into a Type object representing that keyword.
     *
     * @psalm-mutation-free
     */
    private function resolveKeyword(string $type): Type
    {
        $className = $this->keywords[strtolower($type)];

        return new $className();
    }

    /**
     * Resolves the given FQSEN string into an FQSEN object.
     *
     * @psalm-mutation-free
     */
    private function resolveTypedObject(string $type, ?Context $context = null): Object_
    {
        return new Object_($this->fqsenResolver->resolve($type, $context));
    }

    /**
     * Resolves class string
     *
     * @param ArrayIterator<int, (string|null)> $tokens
     *
     * @return array<int, ClassString>
     */
    private function resolveClassString(ArrayIterator $tokens, Context $context): array
    {
        $tokens->next();

        $classType = $this->parseTypes($tokens, $context, self::PARSER_IN_COLLECTION_EXPRESSION);

        $aggreatedObjects = false;
        if ($classType instanceof AggregatedType) {
            foreach ($classType->getIterator() as $typeInner) {
                if (!$typeInner instanceof Object_) {
                    break;
                }
            }

            $aggreatedObjects = true;
        }

        if ($aggreatedObjects) {
            assert($classType instanceof AggregatedType);
            foreach ($classType->getIterator() as $typeInner) {
                if (!$typeInner instanceof Object_ || $typeInner->getFqsen() === null) {
                    throw new RuntimeException(
                        $typeInner . ' is not a class string'
                    );
                }
            }
        } else {
            if (!$classType instanceof Object_ || $classType->getFqsen() === null) {
                throw new RuntimeException(
                    $classType . ' is not a class string'
                );
            }
        }

        $token = $tokens->current();
        if ($token !== '>') {
            if (empty($token)) {
                throw new RuntimeException(
                    'class-string: ">" is missing'
                );
            }

            throw new RuntimeException(
                'Unexpected character "' . $token . '", ">" is missing'
            );
        }

        $return = [];
        if ($aggreatedObjects) {
            assert($classType instanceof AggregatedType);
            foreach ($classType->getIterator() as $typeInner) {
                assert($typeInner instanceof Object_);
                $return[] = new ClassString($typeInner->getFqsen());
            }
        } else {
            assert($classType instanceof Object_);
            $return[] = new ClassString($classType->getFqsen());
        }

        return $return;
    }

    /**
     * Resolves integer ranges
     *
     * @param ArrayIterator<int, (string|null)> $tokens
     */
    private function resolveIntRange(ArrayIterator $tokens): Type
    {
        $tokens->next();

        $token = '';
        $minValue = null;
        $maxValue = null;
        $commaFound = false;
        $tokenCounter = 0;
        while ($tokens->valid()) {
            $tokenCounter++;
            $token = $tokens->current();
            if ($token === null) {
                throw new RuntimeException(
                    'Unexpected nullable character'
                );
            }

            $token = trim($token);

            if ($token === '>') {
                break;
            }

            if ($token === ',') {
                $commaFound = true;
            }

            if ($commaFound === false && $minValue === null) {
                if (is_numeric($token) || $token === 'max' || $token === 'min') {
                    $minValue = $token;
                }
            }

            if ($commaFound === true && $maxValue === null) {
                if (is_numeric($token) || $token === 'max' || $token === 'min') {
                    $maxValue = $token;
                }
            }

            $tokens->next();
        }

        if ($token !== '>') {
            if (empty($token)) {
                throw new RuntimeException(
                    'interface-string: ">" is missing'
                );
            }

            throw new RuntimeException(
                'Unexpected character "' . $token . '", ">" is missing'
            );
        }

        if (!$minValue || !$maxValue || $tokenCounter > 4) {
            throw new RuntimeException(
                'int<min,max> has not the correct format'
            );
        }

        return new IntegerRange($minValue, $maxValue);
    }

    /**
     * Resolves class string
     *
     * @param ArrayIterator<int, (string|null)> $tokens
     */
    private function resolveInterfaceString(ArrayIterator $tokens, Context $context): Type
    {
        $tokens->next();

        $classType = $this->parseTypes($tokens, $context, self::PARSER_IN_COLLECTION_EXPRESSION);

        if (!$classType instanceof Object_ || $classType->getFqsen() === null) {
            throw new RuntimeException(
                $classType . ' is not a interface string'
            );
        }

        $token = $tokens->current();
        if ($token !== '>') {
            if (empty($token)) {
                throw new RuntimeException(
                    'interface-string: ">" is missing'
                );
            }

            throw new RuntimeException(
                'Unexpected character "' . $token . '", ">" is missing'
            );
        }

        return new InterfaceString($classType->getFqsen());
    }

    /**
     * Resolves the collection values and keys
     *
     * @param ArrayIterator<int, (string|null)> $tokens
     *
     * @return Array_|Iterable_|Collection
     */
    private function resolveCollection(ArrayIterator $tokens, Type $classType, Context $context): Type
    {
        $isArray    = ((string) $classType === 'array');
        $isIterable = ((string) $classType === 'iterable');
        $isList     = ((string) $classType === 'list');

        // allow only "array", "iterable" or class name before "<"
        if (
            !$isArray && !$isIterable && !$isList
            && (!$classType instanceof Object_ || $classType->getFqsen() === null)
        ) {
            throw new RuntimeException(
                $classType . ' is not a collection'
            );
        }

        $tokens->next();

        $valueType = $this->parseTypes($tokens, $context, self::PARSER_IN_COLLECTION_EXPRESSION);
        $keyType   = null;

        $token = $tokens->current();
        if ($token !== null && trim($token) === ',' && !$isList) {
            // if we have a comma, then we just parsed the key type, not the value type
            $keyType = $valueType;
            if ($isArray) {
                // check the key type for an "array" collection. We allow only
                // strings or integers.
                if (
                    !$keyType instanceof ArrayKey &&
                    !$keyType instanceof String_ &&
                    !$keyType instanceof Integer &&
                    !$keyType instanceof Compound
                ) {
                    throw new RuntimeException(
                        'An array can have only integers or strings as keys'
                    );
                }

                if ($keyType instanceof Compound) {
                    foreach ($keyType->getIterator() as $item) {
                        if (
                            !$item instanceof ArrayKey &&
                            !$item instanceof String_ &&
                            !$item instanceof Integer
                        ) {
                            throw new RuntimeException(
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

        $token = $tokens->current();
        if ($token !== '>') {
            if (empty($token)) {
                throw new RuntimeException(
                    'Collection: ">" is missing'
                );
            }

            throw new RuntimeException(
                'Unexpected character "' . $token . '", ">" is missing'
            );
        }

        if ($isArray) {
            return new Array_($valueType, $keyType);
        }

        if ($isIterable) {
            return new Iterable_($valueType, $keyType);
        }

        if ($isList) {
            return new List_($valueType);
        }

        if ($classType instanceof Object_) {
            return $this->makeCollectionFromObject($classType, $valueType, $keyType);
        }

        throw new RuntimeException('Invalid $classType provided');
    }

    /**
     * @psalm-pure
     */
    private function makeCollectionFromObject(Object_ $object, Type $valueType, ?Type $keyType = null): Collection
    {
        return new Collection($object->getFqsen(), $valueType, $keyType);
    }
}
