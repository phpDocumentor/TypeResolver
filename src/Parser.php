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


namespace phpDocumentor\Reflection;


/* This is an automatically GENERATED file, which should not be manually edited.
 */
class Parser extends \phpDocumentor\Reflection\ParserAbstract
{
    protected $tokenToSymbolMapSize = 268;
    protected $actionTableSize      = 18;
    protected $gotoTableSize        = 7;

    protected $invalidSymbol       = 13;
    protected $errorSymbol         = 1;
    protected $defaultAction       = -32766;
    protected $unexpectedTokenRule = 32767;

    protected $numNonLeafStates    = 14;

    protected $YY2TBLSTATE = 9;
    protected $YYNLSTATES  = 14;

    protected $symbolToName = array(
        "EOF",
        "error",
        "T_NULLABLE",
        "T_COMPOUND_OPERATOR",
        "T_INTERSECTION_OPERATOR",
        "T_OPEN_SQUARE_BRACKET",
        "T_CLOSE_SQUARE_BRACKET",
        "T_OPEN_PARENTHESIS",
        "T_CLOSE_PARENTHESIS",
        "T_TYPE",
        "T_FULLY_QUALIFIED_NAME",
        "T_QUALIFIED_NAME",
        "T_VOID"
    );

    protected $tokenToSymbol = array(
            0,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,   13,   13,   13,   13,
           13,   13,   13,   13,   13,   13,    1,    2,    3,    4,
            5,    6,    7,    8,    9,   10,   11,   12
    );

    protected $action = array(
            1,   23,   25,   26,    1,    2,    3,    0,    3,    2,
           33,   32,    4,    5,    0,   12,    0,   28
    );

    protected $actionCheck = array(
            2,    9,   10,   11,    2,    7,    3,    0,    3,    7,
           12,    8,    4,    4,   -1,    5,   -1,    6
    );

    protected $actionBase = array(
           -2,    2,    2,    2,   -8,   -8,    3,    5,    5,    7,
            8,    9,   11,   10,   -8,   -8,   -8,   -8,    0,    0,
           10,   10,   10
    );

    protected $actionDefault = array(
        32767,32767,32767,32767,32767,32767,32767,    2,   13,32767,
            3,    6,32767,   15
    );

    protected $goto = array(
            8,    6,   13,    0,    0,   30,   31
    );

    protected $gotoCheck = array(
            3,    3,    3,   -1,   -1,    4,    4
    );

    protected $gotoBase = array(
            0,    0,    0,   -1,    1,    0,    0,    0,    0,    0,
            0
    );

    protected $gotoDefault = array(
        -32768,    9,   15,    7,   10,   18,   19,   11,   21,   22,
           24
    );

    protected $ruleToNonTerminal = array(
            0,    1,    1,    3,    3,    3,    3,    3,    3,    4,
            4,   10,   10,    5,    8,    6,    7,    7,    9,    2
    );

    protected $ruleToLength = array(
            1,    1,    1,    1,    1,    1,    1,    1,    1,    1,
            1,    1,    1,    2,    3,    3,    3,    3,    3,    1
    );

    protected function initReduceCallbacks() {
        $this->reduceCallbacks = [
            0 => function ($stackPos) {
            $this->semValue = $this->semStack[$stackPos];
        },
            1 => function ($stackPos) {
            $this->semValue = $this->semStack[$stackPos];
        },
            2 => function ($stackPos) {
            $this->semValue = $this->semStack[$stackPos];
        },
            3 => function ($stackPos) {
            $this->semValue = $this->semStack[$stackPos];
        },
            4 => function ($stackPos) {
            $this->semValue = $this->semStack[$stackPos];
        },
            5 => function ($stackPos) {
            $this->semValue = $this->semStack[$stackPos];
        },
            6 => function ($stackPos) {
            $this->semValue = $this->semStack[$stackPos];
        },
            7 => function ($stackPos) {
            $this->semValue = $this->semStack[$stackPos];
        },
            8 => function ($stackPos) {
            $this->semValue = $this->semStack[$stackPos];
        },
            9 => function ($stackPos) {
             $this->semValue = $this->resolveKeyword($this->semStack[$stackPos-(1-1)]); 
            },
            10 => function ($stackPos) {
             $this->semValue = new Types\Object_($this->semStack[$stackPos-(1-1)]); 
            },
            11 => function ($stackPos) {
             $this->semValue = new Fqsen($this->semStack[$stackPos-(1-1)]); 
            },
            12 => function ($stackPos) {
             $this->semValue = $this->fqsenResolver->resolve($this->semStack[$stackPos-(1-1)], $this->context); 
            },
            13 => function ($stackPos) {
             $this->semValue = new Types\Nullable($this->semStack[$stackPos-(2-2)]); 
            },
            14 => function ($stackPos) {
             $this->semValue = new Types\Array_($this->semStack[$stackPos-(3-1)]); 
            },
            15 => function ($stackPos) {
             $this->semValue = new Types\Compound([$this->semStack[$stackPos-(3-1)], $this->semStack[$stackPos-(3-3)]]); 
            },
            16 => function ($stackPos) {
             $this->semValue = new Types\Intersection([$this->semStack[$stackPos-(3-1)], $this->semStack[$stackPos-(3-3)]]); 
            },
            17 => function ($stackPos) {
             $this->semValue = new Types\Intersection(array_merge($this->semStack[$stackPos-(3-1)]->getAll(), [$this->semStack[$stackPos-(3-3)]])); 
            },
            18 => function ($stackPos) {
             $this->semValue = new Types\Expression($this->semStack[$stackPos-(3-2)]); 
            },
            19 => function ($stackPos) {
             $this->semValue = new Types\Void_(); 
            },
        ];
    }
}
