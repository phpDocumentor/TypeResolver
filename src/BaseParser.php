<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @link      http://phpdoc.org
 */

declare(strict_types=1);

namespace phpDocumentor\Reflection;

use InvalidArgumentException;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use RangeException;
use RuntimeException;
use function class_exists;
use function class_implements;
use function count;
use function implode;
use function in_array;
use function sprintf;
use function strtolower;

abstract class BaseParser
{
    private const SYMBOL_NONE = -1;

    /*
     * The following members will be filled with generated parsing data:
     */

    /** @var int Size of $tokenToSymbol map */
    protected $tokenToSymbolMapSize;
    /** @var int Size of $action table */
    protected $actionTableSize;
    /** @var int Size of $goto table */
    protected $gotoTableSize;

    /** @var int Symbol number signifying an invalid token */
    protected $invalidSymbol;
    /** @var int Symbol number of error recovery token */
    protected $errorSymbol;
    /** @var int Action number signifying default action */
    protected $defaultAction;
    /** @var int Rule number signifying that an unexpected token was encountered */
    protected $unexpectedTokenRule;
    /** @var int states */
    protected $YY2TBLSTATE;
    /** @var int Number of non-leaf states */
    protected $numNonLeafStates;

    /** @var int[] Map of lexer tokens to internal symbols */
    protected $tokenToSymbol;
    /** @var string[] Map of symbols to their names */
    protected $symbolToName;
    /** @var string[] Names of the production rules (only necessary for debugging) */
    protected $productions;

    /**
     * @var int[] Map of states to a displacement into the $action table. The corresponding action for this
     *             state/symbol pair is $action[$actionBase[$state] + $symbol]. If $actionBase[$state] is 0, the
     * action is defaulted, i.e. $actionDefault[$state] should be used instead.
     */
    protected $actionBase;
    /** @var int[] Table of actions. Indexed according to $actionBase comment. */
    protected $action;
    /**
     * @var int[] Table indexed analogously to $action. If $actionCheck[$actionBase[$state] + $symbol] != $symbol
     *             then the action is defaulted, i.e. $actionDefault[$state] should be used instead.
     */
    protected $actionCheck;
    /** @var int[] Map of states to their default action */
    protected $actionDefault;
    /** @var callable[] Semantic action callbacks */
    protected $reduceCallbacks;


    /** @var TypeLexer */
    private $lexer;

    /** @var FqsenResolver */
    protected $fqsenResolver;

    /** @var Context */
    protected $context;

    /** @var Type|Type[]|null Temporary value containing the result of last semantic action (reduction) */
    protected $semValue;

    /** @var Type[] Semantic value stack (contains values of tokens and semantic action results) */
    protected $semStack;

    /**
     * @var array<string, string> List of recognized keywords and unto which Value Object they map
     * @psalm-var array<string, class-string<Type>>
     */
    private $keywords = [
        'string' => Types\String_::class,
        'class-string' => Types\ClassString::class,
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
    ];

    /**
     * @return void
     */
    //phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
    abstract protected function initReduceCallbacks();

    public function __construct(?FqsenResolver $fqsenResolver)
    {
        $this->lexer = new TypeLexer();
        $this->fqsenResolver = $fqsenResolver ?? new FqsenResolver();
        $this->initReduceCallbacks();
    }

    public function parse(string $type, Context $context) : Type
    {
        $this->context = $context;
        $this->lexer->setInput($type);
        $this->lexer->moveNext();

        // Start off in the initial state and keep a stack of previous states
        $state = 0;
        $stateStack = [$state];
        $symbol = self::SYMBOL_NONE;

        // Semantic value stack (contains values of tokens and semantic action results)
        $this->semStack = [];
        $this->semValue = null;

        $stackPos = 0;

        while (true) {
            //$this->traceNewState($state, $symbol);
            if ($this->actionBase[$state] === 0) {
                $rule = $this->actionDefault[$state];
            } else {
                if ($symbol === self::SYMBOL_NONE) {
                    $this->lexer->moveNext();
                    $tokenId    = $this->lexer->token['type'] ?? 0;
                    $tokenValue = $this->lexer->token['value'];

                    // map the lexer token id to the internally used symbols
                    $symbol = $tokenId >= 0 && $tokenId < $this->tokenToSymbolMapSize
                        ? $this->tokenToSymbol[$tokenId]
                        : $this->invalidSymbol;

                    if ($symbol === $this->invalidSymbol) {
                        throw new RangeException(sprintf(
                            'The lexer returned an invalid token (id=%d, value=%s)',
                            $tokenId,
                            $tokenValue
                        ));
                    }

                    //$this->traceRead($symbol);
                }

                $idx = $this->actionBase[$state] + $symbol;
                if ((($idx >= 0 && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol)
                        || ($state < $this->YY2TBLSTATE
                            && ($idx = $this->actionBase[$state + $this->numNonLeafStates] + $symbol) >= 0
                            && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol))
                    && ($action = $this->action[$idx]) !== $this->defaultAction) {
                    if ($action > 0) {
                        /** shift */
                        //$this->traceShift($symbol);

                        ++$stackPos;
                        $stateStack[$stackPos] = $state = $action;
                        $this->semStack[$stackPos] = $tokenValue;
                        $symbol = self::SYMBOL_NONE;

                        if ($action < $this->numNonLeafStates) {
                            continue;
                        }

                        /* $yyn >= numNonLeafStates means shift-and-reduce */
                        $rule = $action - $this->numNonLeafStates;
                    } else {
                        $rule = -$action;
                    }
                } else {
                    $rule = $this->actionDefault[$state];
                }
            }

            for (;;) {
                if ($rule === 0) {
                    /* accept */
                    // $this->traceAccept();

                    return $this->semValue;
                }

                if ($rule === $this->unexpectedTokenRule) {
                    /* error */
                    $msg = $this->getErrorMessage($symbol, $state);

                    throw new RuntimeException($msg);
                }

                /* reduce */
                // $this->traceReduce($rule);
                $this->reduceCallbacks[$rule]($stackPos);

                $stackPos    -= $this->ruleToLength[$rule];
                $nonTerminal = $this->ruleToNonTerminal[$rule];
                $idx         = $this->gotoBase[$nonTerminal] + $stateStack[$stackPos];
                if ($idx >= 0 && $idx < $this->gotoTableSize && $this->gotoCheck[$idx] === $nonTerminal) {
                    $state = $this->goto[$idx];
                } else {
                    $state = $this->gotoDefault[$nonTerminal];
                }

                ++$stackPos;
                $stateStack[$stackPos]     = $state;
                $this->semStack[$stackPos] = $this->semValue;

                if ($state < $this->numNonLeafStates) {
                    break;
                }

                /* >= numNonLeafStates means shift-and-reduce */
                $rule = $state - $this->numNonLeafStates;
            }
        }

        throw new RuntimeException('Reached end of parser loop');
    }

    /**
     * Format error message including expected tokens.
     *
     * @param int $symbol Unexpected symbol
     * @param int $state  State at time of error
     *
     * @return string Formatted error message
     */
    protected function getErrorMessage(int $symbol, int $state) : string
    {
        $expectedString = '';
        if ($expected = $this->getExpectedTokens($state)) {
            $expectedString = ', expecting ' . implode(' or ', $expected);
        }

        return 'Type syntax error, unexpected ' . $this->symbolToName[$symbol] . $expectedString;
    }

    /**
     * Get limited number of expected tokens in given state.
     *
     * @param int $state State
     *
     * @return string[] Expected tokens. If too many, an empty array is returned.
     */
    protected function getExpectedTokens(int $state) : array
    {
        $expected = [];

        $base = $this->actionBase[$state];
        foreach ($this->symbolToName as $symbol => $name) {
            $idx = $base + $symbol;
            if ($idx < 0 || $idx >= $this->actionTableSize || ($this->actionCheck[$idx] !== $symbol
                && $state >= $this->YY2TBLSTATE)
                || (isset($this->actionBase[$state + $this->numNonLeafStates]) &&
                    $idx = $this->actionBase[$state + $this->numNonLeafStates] + $symbol) < 0
                || $idx >= $this->actionTableSize ||
                (isset($this->actionCheck[$idx]) && $this->actionCheck[$idx] !== $symbol)
            ) {
                continue;
            }

            if (
                (isset($this->action[$idx]) &&
                    (
                        $this->action[$idx] === $this->unexpectedTokenRule
                        || $this->action[$idx] === $this->defaultAction
                    )
                )
                || $symbol === $this->errorSymbol
            ) {
                continue;
            }

            if (count($expected) === 4) {
                /* Too many expected tokens */
                return [];
            }

            $expected[] = $name;
        }

        return $expected;
    }

    /**
     * Resolves the given keyword (such as `string`) into a Type object representing that keyword.
     *
     * @psalm-pure
     */
    protected function resolveKeyword(string $type) : Type
    {
        $className = $this->keywords[strtolower($type)];

        return new $className();
    }

    /**
     * Resolves the collection values and keys
     *
     * @return Array_|Iterable_
     */
    protected function resolveCollection(string $collectionType, ?Type $valueType = null, ?Type $keyType = null) : Type
    {
        $isArray    = ($collectionType === 'array');
        $isIterable = ($collectionType === 'iterable');

        // allow only "array", "iterable" or class name before "<"
        if (!$isArray && !$isIterable) {
            throw new RuntimeException(
                $collectionType . ' is not a collection'
            );
        }

        if ($isArray) {
            // check the key type for an "array" collection. We allow only
            // strings or integers.
            if ($keyType !== null) {
                if (!$keyType instanceof String_ &&
                    !$keyType instanceof Integer &&
                    !$keyType instanceof Compound
                ) {
                    throw new RuntimeException(
                        'An array can have only integers or strings as keys'
                    );
                }

                if ($keyType instanceof Compound) {
                    foreach ($keyType->getIterator() as $item) {
                        if (!$item instanceof String_ &&
                            !$item instanceof Integer
                        ) {
                            throw new RuntimeException(
                                'An array can have only integers or strings as keys'
                            );
                        }
                    }
                }
            }
        }

        if ($isArray) {
            return new Array_($valueType, $keyType);
        }

        if ($isIterable) {
            return new Iterable_($valueType, $keyType);
        }

        throw new RuntimeException('Invalid $classType provided');
    }

    /**
     * Adds a keyword to the list of Keywords and associates it with a specific Value Object.
     *
     * @psalm-param class-string<Type> $typeClassName
     */
    public function addKeyword(string $keyword, string $typeClassName) : void
    {
        if (!class_exists($typeClassName)) {
            throw new InvalidArgumentException(
                'The Value Object that needs to be created with a keyword "' . $keyword . '" must be an existing class'
                . ' but we could not find the class ' . $typeClassName
            );
        }

        if (!in_array(Type::class, class_implements($typeClassName), true)) {
            throw new InvalidArgumentException(
                'The class "' . $typeClassName . '" must implement the interface "phpDocumentor\Reflection\Type"'
            );
        }

        $this->lexer->addPseudoType($keyword);

        $this->keywords[$keyword] = $typeClassName;
    }

//    protected function traceNewState($state, $symbol) : void
//    {
//        echo '% State ' . $state
//            . ', Lookahead ' . ($symbol === self::SYMBOL_NONE ? '--none--' : $this->symbolToName[$symbol]) . "\n";
//    }
//
//    protected function traceRead($symbol) : void
//    {
//        echo '% Reading ' . $this->symbolToName[$symbol] . "\n";
//    }
//
//    protected function traceShift($symbol) : void
//    {
//        echo '% Shift ' . $this->symbolToName[$symbol] . "\n";
//    }
//
//    protected function traceAccept() : void
//    {
//        echo "% Accepted.\n";
//    }
//
//    protected function traceReduce($n) : void
//    {
//        echo '% Reduce by (' . $n . ') ' . $this->productions[$n] . "\n";
//    }
//
//    protected function tracePop($state) : void
//    {
//        echo '% Recovering, uncovered state ' . $state . "\n";
//    }
//
//    protected function traceDiscard($symbol) : void
//    {
//        echo '% Discard ' . $this->symbolToName[$symbol] . "\n";
//    }
}
