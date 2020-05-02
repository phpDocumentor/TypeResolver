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
    protected $tokenToSymbolMapSize = 272;
    protected $actionTableSize      = 20;
    protected $gotoTableSize        = 7;

    protected $invalidSymbol       = 17;
    protected $errorSymbol         = 1;
    protected $defaultAction       = -32766;
    protected $unexpectedTokenRule = 32767;

    protected $numNonLeafStates    = 18;

    protected $YY2TBLSTATE = 10;
    protected $YYNLSTATES  = 18;

    protected $symbolToName = array(
        "EOF",
        "error",
        "T_NULLABLE",
        "T_OPEN_PARENTHESIS",
        "T_COMMA",
        "T_COMPOUND_OPERATOR",
        "T_INTERSECTION_OPERATOR",
        "T_OPEN_SQUARE_BRACKET",
        "T_LESS_THAN",
        "T_CLOSE_SQUARE_BRACKET",
        "T_CLOSE_PARENTHESIS",
        "T_GREATER_THAN",
        "T_TYPE",
        "T_FULLY_QUALIFIED_NAME",
        "T_QUALIFIED_NAME",
        "T_VOID",
        "T_COLLECTION_TYPE"
    );

    protected $tokenToSymbol = array(
            0,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,    1,    2,    3,    4,
            5,    6,    7,    8,    9,   10,   11,   12,   13,   14,
           15,   16
    );

    protected $action = array(
            1,    2,    4,    5,   15,    0,    6,    3,   15,   41,
           21,   39,   40,   36,   12,   29,    0,   38,    0,   37
    );

    protected $actionCheck = array(
            2,    3,    5,    6,    7,    0,    4,    8,    7,   15,
           12,   13,   14,   11,   16,    9,   -1,   10,   -1,   11
    );

    protected $actionBase = array(
           -6,   -2,   -2,   -2,   -2,   -2,   -2,    2,    7,    8,
           -3,   -3,   -1,   -1,    5,    6,    1,    1,   -2,    0,
            0,    0,    0,    0,    0,   -3,   -3,   -3
    );

    protected $actionDefault = array(
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
            2,   10,   15,   17,32767,32767,   12,   13
    );

    protected $goto = array(
           11,    8,    7,   16,   17,    9,   34
    );

    protected $gotoCheck = array(
            3,    3,    3,    3,    3,    3,   10
    );

    protected $gotoBase = array(
            0,    0,    0,   -1,    0,    0,    0,    0,    0,    0,
           -7,    0
    );

    protected $gotoDefault = array(
        -32768,   14,   19,   10,   22,   23,   24,   25,   26,   27,
           32,   13
    );

    protected $ruleToNonTerminal = array(
            0,    1,    1,    3,    3,    3,    3,    3,    3,    3,
            4,    8,    5,    6,    9,    9,    9,    9,   10,   10,
            7,   11,   11,    2
    );

    protected $ruleToLength = array(
            1,    1,    1,    1,    1,    1,    1,    1,    1,    1,
            2,    3,    3,    3,    2,    1,    2,    1,    3,    5,
            3,    1,    1,    1
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
             $this->semValue = $this->resolveKeyword($this->semStack[$stackPos-(1-1)]); 
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
            $this->semValue = $this->semStack[$stackPos];
        },
            10 => function ($stackPos) {
             $this->semValue = new Types\Nullable($this->semStack[$stackPos-(2-2)]); 
            },
            11 => function ($stackPos) {
             $this->semValue = new Types\Array_($this->semStack[$stackPos-(3-1)]); 
            },
            12 => function ($stackPos) {
             $this->semValue = new Types\Compound([$this->semStack[$stackPos-(3-1)], $this->semStack[$stackPos-(3-3)]]); 
            },
            13 => function ($stackPos) {
             $this->semValue = new Types\Intersection([$this->semStack[$stackPos-(3-1)], $this->semStack[$stackPos-(3-3)]]); 
            },
            14 => function ($stackPos) {
             $this->semValue = $this->resolveCollection($this->semStack[$stackPos-(2-1)], $this->semStack[$stackPos-(2-2)][0], $this->semStack[$stackPos-(2-2)][1]); 
            },
            15 => function ($stackPos) {
             $this->semValue = $this->resolveCollection($this->semStack[$stackPos-(1-1)]); 
            },
            16 => function ($stackPos) {
             $this->semValue = new Types\Collection($this->semStack[$stackPos-(2-1)], $this->semStack[$stackPos-(2-2)][0], $this->semStack[$stackPos-(2-2)][1]); 
            },
            17 => function ($stackPos) {
             $this->semValue = new Types\Object_($this->semStack[$stackPos-(1-1)]); 
            },
            18 => function ($stackPos) {
             $this->semValue = [$this->semStack[$stackPos-(3-2)], null]; 
            },
            19 => function ($stackPos) {
             $this->semValue = [$this->semStack[$stackPos-(5-4)], $this->semStack[$stackPos-(5-2)]]; 
            },
            20 => function ($stackPos) {
             $this->semValue = new Types\Expression($this->semStack[$stackPos-(3-2)]); 
            },
            21 => function ($stackPos) {
             $this->semValue = new Fqsen($this->semStack[$stackPos-(1-1)]); 
            },
            22 => function ($stackPos) {
             $this->semValue = $this->fqsenResolver->resolve($this->semStack[$stackPos-(1-1)], $this->context); 
            },
            23 => function ($stackPos) {
             $this->semValue = new Types\Void_(); 
            },
        ];
    }
}
