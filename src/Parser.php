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
class Parser extends \phpDocumentor\Reflection\BaseParser
{
    protected $tokenToSymbolMapSize = 274;
    protected $actionTableSize      = 26;
    protected $gotoTableSize        = 8;

    protected $invalidSymbol       = 19;
    protected $errorSymbol         = 1;
    protected $defaultAction       = -32766;
    protected $unexpectedTokenRule = 32767;

    protected $numNonLeafStates    = 21;

    protected $YY2TBLSTATE = 10;
    protected $YYNLSTATES  = 21;

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
        "T_COLLECTION_TYPE",
        "T_PSEUDO_TYPE",
        "T_CLASS_STRING"
    );

    protected $tokenToSymbol = array(
            0,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,   19,   19,   19,   19,
           19,   19,   19,   19,   19,   19,    1,    2,    3,    4,
            5,    6,    7,    8,    9,   10,   11,   12,   13,   14,
           15,   16,   17,   18
    );

    protected $action = array(
            1,    2,    4,    5,   17,    0,    6,   44,   45,    3,
           24,   44,   45,   41,   13,   25,   15,   17,   34,   12,
            0,   48,   43,    0,   47,   42
    );

    protected $actionCheck = array(
            2,    3,    5,    6,    7,    0,    4,   13,   14,    8,
           12,   13,   14,   11,   16,   17,   18,    7,    9,    8,
           -1,   15,   10,   -1,   11,   11
    );

    protected $actionBase = array(
            6,   -2,   -2,   -2,   -2,   -2,   -2,    2,   12,   14,
           -3,   -3,   -6,    1,    1,   11,    5,    9,   13,   10,
           10,   -2,    0,    0,    0,    0,    0,    0,   -3,   -3,
           -3
    );

    protected $actionDefault = array(
        32767,32767,32767,32767,32767,32767,32767,32767,32767,32767,
            2,   12,32767,   17,   19,   25,32767,32767,32767,   14,
           15
    );

    protected $goto = array(
           11,    8,    7,   19,   20,    9,   18,   39
    );

    protected $gotoCheck = array(
            3,    3,    3,    3,    3,    3,   12,   11
    );

    protected $gotoBase = array(
            0,    0,    0,   -1,    0,    0,    0,    0,    0,    0,
            0,   -7,   -6
    );

    protected $gotoDefault = array(
        -32768,   16,   22,   10,   26,   27,   28,   29,   30,   31,
           32,   37,   14
    );

    protected $ruleToNonTerminal = array(
            0,    1,    1,    3,    3,    3,    3,    3,    3,    3,
            3,    3,    4,    8,    5,    6,    9,    9,    9,    9,
           11,   11,    7,   12,   12,   10,   10,    2
    );

    protected $ruleToLength = array(
            1,    1,    1,    1,    1,    1,    1,    1,    1,    1,
            1,    1,    2,    3,    3,    3,    2,    1,    2,    1,
            3,    5,    3,    1,    1,    1,    4,    1
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
             $this->semValue = $this->resolveKeyword($this->semStack[$stackPos-(1-1)]); 
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
            $this->semValue = $this->semStack[$stackPos];
        },
            11 => function ($stackPos) {
            $this->semValue = $this->semStack[$stackPos];
        },
            12 => function ($stackPos) {
             $this->semValue = new Types\Nullable($this->semStack[$stackPos-(2-2)]); 
            },
            13 => function ($stackPos) {
             $this->semValue = new Types\Array_($this->semStack[$stackPos-(3-1)]); 
            },
            14 => function ($stackPos) {
             $this->semValue = new Types\Compound([$this->semStack[$stackPos-(3-1)], $this->semStack[$stackPos-(3-3)]]); 
            },
            15 => function ($stackPos) {
             $this->semValue = new Types\Intersection([$this->semStack[$stackPos-(3-1)], $this->semStack[$stackPos-(3-3)]]); 
            },
            16 => function ($stackPos) {
             $this->semValue = $this->resolveCollection($this->semStack[$stackPos-(2-1)], $this->semStack[$stackPos-(2-2)][0], $this->semStack[$stackPos-(2-2)][1]); 
            },
            17 => function ($stackPos) {
             $this->semValue = $this->resolveCollection($this->semStack[$stackPos-(1-1)]); 
            },
            18 => function ($stackPos) {
             $this->semValue = new Types\Collection($this->semStack[$stackPos-(2-1)], $this->semStack[$stackPos-(2-2)][0], $this->semStack[$stackPos-(2-2)][1]); 
            },
            19 => function ($stackPos) {
             $this->semValue = new Types\Object_($this->semStack[$stackPos-(1-1)]); 
            },
            20 => function ($stackPos) {
             $this->semValue = [$this->semStack[$stackPos-(3-2)], null]; 
            },
            21 => function ($stackPos) {
             $this->semValue = [$this->semStack[$stackPos-(5-4)], $this->semStack[$stackPos-(5-2)]]; 
            },
            22 => function ($stackPos) {
             $this->semValue = new Types\Expression($this->semStack[$stackPos-(3-2)]); 
            },
            23 => function ($stackPos) {
             $this->semValue = new Fqsen($this->semStack[$stackPos-(1-1)]); 
            },
            24 => function ($stackPos) {
             $this->semValue = $this->fqsenResolver->resolve($this->semStack[$stackPos-(1-1)], $this->context); 
            },
            25 => function ($stackPos) {
             $this->semValue = new Types\ClassString(); 
            },
            26 => function ($stackPos) {
             $this->semValue = new Types\ClassString($this->semStack[$stackPos-(4-3)]); 
            },
            27 => function ($stackPos) {
             $this->semValue = new Types\Void_(); 
            },
        ];
    }
}
