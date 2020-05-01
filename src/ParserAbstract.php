<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @link      http://phpdoc.org
 *
 */

declare(strict_types=1);

namespace phpDocumentor\Reflection;

use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Object_;

abstract class ParserAbstract
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

    protected $YY2TBLSTATE;
    /** @var int Number of non-leaf states */
    protected $numNonLeafStates;

    /** @var int[] Map of lexer tokens to internal symbols */
    protected $tokenToSymbol;
    /** @var string[] Map of symbols to their names */
    protected $symbolToName;
    /** @var array Names of the production rules (only necessary for debugging) */
    protected $productions;

    /** @var int[] Map of states to a displacement into the $action table. The corresponding action for this
     *             state/symbol pair is $action[$actionBase[$state] + $symbol]. If $actionBase[$state] is 0, the
    action is defaulted, i.e. $actionDefault[$state] should be used instead. */
    protected $actionBase;
    /** @var int[] Table of actions. Indexed according to $actionBase comment. */
    protected $action;
    /** @var int[] Table indexed analogously to $action. If $actionCheck[$actionBase[$state] + $symbol] != $symbol
     *             then the action is defaulted, i.e. $actionDefault[$state] should be used instead. */
    protected $actionCheck;
    /** @var int[] Map of states to their default action */
    protected $actionDefault;
    /** @var callable[] Semantic action callbacks */
    protected $reduceCallbacks;


    /**
     * @var TypeLexer
     */
    private $lexer;

    /**
     * @var FqsenResolver
     */
    protected $fqsenResolver;

    /** @var Context */
    protected $context;

    /** @var Type Temporary value containing the result of last semantic action (reduction) */
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

    private $errorState = 0;

    abstract protected function initReduceCallbacks();

    public function __construct()
    {
        $this->lexer = new TypeLexer();
        $this->fqsenResolver = new FqsenResolver();
        $this->initReduceCallbacks();
    }

    public function parse(string $type, Context $context)
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
            $this->traceNewState($state, $symbol);
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
                        throw new \RangeException(sprintf(
                            'The lexer returned an invalid token (id=%d, value=%s)',
                            $tokenId, $tokenValue
                        ));
                    }

                    $this->traceRead($symbol);
                }

                $idx = $this->actionBase[$state] + $symbol;
                if ((($idx >= 0 && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol)
                        || ($state < $this->YY2TBLSTATE
                            && ($idx = $this->actionBase[$state + $this->numNonLeafStates] + $symbol) >= 0
                            && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol))
                    && ($action = $this->action[$idx]) !== $this->defaultAction) {

                    if ($action > 0) {
                        /** shift */
                        $this->traceShift($symbol);

                        ++$stackPos;
                        $stateStack[$stackPos] = $state = $action;
                        $this->semStack[$stackPos] = $tokenValue;
                        $symbol = self::SYMBOL_NONE;

                        if ($this->errorState) {
                            --$this->errorState;
                        }

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
                    $this->traceAccept();
                    return $this->semValue;
                } elseif ($rule !== $this->unexpectedTokenRule) {
                    /* reduce */
                    $this->traceReduce($rule);
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
                } else {
                    /* error */
                    switch ($this->errorState) {
                        case 0:
                            $msg = $this->getErrorMessage($symbol, $state);
                            throw new \RuntimeException($msg);
                        // Break missing intentionally
                        case 1:
                        case 2:
                            $this->errorState = 3;

                            // Pop until error-expecting state uncovered
                            while (!(
                                    (($idx = $this->actionBase[$state] + $this->errorSymbol) >= 0
                                        && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $this->errorSymbol)
                                    || ($state < $this->YY2TBLSTATE
                                        && ($idx = $this->actionBase[$state + $this->numNonLeafStates] + $this->errorSymbol) >= 0
                                        && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $this->errorSymbol)
                                ) || ($action = $this->action[$idx]) === $this->defaultAction) { // Not totally sure about this
                                if ($stackPos <= 0) {
                                    // Could not recover from error
                                    return null;
                                }
                                $state = $stateStack[--$stackPos];
                                $this->tracePop($state);
                            }

                            $this->traceShift($this->errorSymbol);
                            ++$stackPos;
                            $stateStack[$stackPos] = $state = $action;
                            break;

                        case 3:
                            if ($symbol === 0) {
                                // Reached EOF without recovering from error
                                return null;
                            }

                            $this->traceDiscard($symbol);
                            $symbol = self::SYMBOL_NONE;
                            break 2;
                    }
                }

                if ($state < $this->numNonLeafStates) {
                    break;
                }

                /* >= numNonLeafStates means shift-and-reduce */
                $rule = $state - $this->numNonLeafStates;
            }
        }


        throw new \RuntimeException('Reached end of parser loop');
    }

    /**
     * Format error message including expected tokens.
     *
     * @param int $symbol Unexpected symbol
     * @param int $state  State at time of error
     *
     * @return string Formatted error message
     */
    protected function getErrorMessage(int $symbol, int $state) : string {
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
    protected function getExpectedTokens(int $state) : array {
        $expected = [];

        $base = $this->actionBase[$state];
        foreach ($this->symbolToName as $symbol => $name) {
            $idx = $base + $symbol;
            if ($idx >= 0 && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol
                || $state < $this->YY2TBLSTATE
                && ($idx = $this->actionBase[$state + $this->numNonLeafStates] + $symbol) >= 0
                && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol
            ) {
                if ($this->action[$idx] !== $this->unexpectedTokenRule
                    && $this->action[$idx] !== $this->defaultAction
                    && $symbol !== $this->errorSymbol
                ) {
                    if (count($expected) === 4) {
                        /* Too many expected tokens */
                        return [];
                    }

                    $expected[] = $name;
                }
            }
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

    protected function traceNewState($state, $symbol) {
        echo '% State ' . $state
            . ', Lookahead ' . ($symbol == self::SYMBOL_NONE ? '--none--' : $this->symbolToName[$symbol]) . "\n";
    }
    protected function traceRead($symbol) {
        echo '% Reading ' . $this->symbolToName[$symbol] . "\n";
    }
    protected function traceShift($symbol) {
        echo '% Shift ' . $this->symbolToName[$symbol] . "\n";
    }
    protected function traceAccept() {
        echo "% Accepted.\n";
    }
    protected function traceReduce($n) {
        echo '% Reduce by (' . $n . ') ' . $this->productions[$n] . "\n";
    }
    protected function tracePop($state) {
        echo '% Recovering, uncovered state ' . $state . "\n";
    }
    protected function traceDiscard($symbol) {
        echo '% Discard ' . $this->symbolToName[$symbol] . "\n";
    }
}
