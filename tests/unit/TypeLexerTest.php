<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection;

use PHPUnit\Framework\TestCase;

final class TypeLexerTest extends TestCase
{
    /** @var TypeLexer */
    private $lexer;

    public function setUp() : void
    {
        $this->lexer = new TypeLexer();
    }

    /**
     * @dataProvider provideExamples
     */
    public function testWillItLex(string $input, array $tokens) : void
    {
        $this->lexer->setInput($input);

        $result =  [];
        while ($this->lexer->moveNext()) {
            $result[] = $this->lexer->lookahead['type'];
        }

        $this->assertSame($tokens, $result);
    }

    public function provideExamples(): array
    {
        return [
            'a null value' => ['null', [TypeLexer::T_NULL]],
            'an uppercase null value' => ['NULL', [TypeLexer::T_NULL]],

            'voids' => ['void', [TypeLexer::T_VOID]],

            'strings as primitives' => ['string', [TypeLexer::T_PRIMITIVE_TYPE]],
            'integers as primitives' => ['integer', [TypeLexer::T_PRIMITIVE_TYPE]],
            'booleans as primitives' => ['boolean', [TypeLexer::T_PRIMITIVE_TYPE]],
            'floats as primitives' => ['float', [TypeLexer::T_PRIMITIVE_TYPE]],
            'scalar as a primitive' => ['scalar', [TypeLexer::T_PRIMITIVE_TYPE]],
            'object as a primitive' => ['object', [TypeLexer::T_PRIMITIVE_TYPE]],
            'resources as primitives' => ['resource', [TypeLexer::T_PRIMITIVE_TYPE]],
            'callables as primitives' => ['callable', [TypeLexer::T_PRIMITIVE_TYPE]],

            'iterable as a collection' => ['iterable', [TypeLexer::T_COLLECTION_TYPE]],
            'array as a collection' => ['array', [TypeLexer::T_COLLECTION_TYPE]],

            'int as a pseudo-type' => ['int', [TypeLexer::T_PSEUDO_TYPE]],
            'bool as a pseudo-type' => ['bool', [TypeLexer::T_PSEUDO_TYPE]],
            'mixed as a pseudo-type' => ['mixed', [TypeLexer::T_PSEUDO_TYPE]],
            'false as a pseudo-type' => ['false', [TypeLexer::T_PSEUDO_TYPE]],
            'an uppercase false as a pseudo-type' => ['FALSE', [TypeLexer::T_PSEUDO_TYPE]],
            'true as a pseudo-type' => ['true', [TypeLexer::T_PSEUDO_TYPE]],
            'an uppercase int as a pseudo-type' => ['TRUE', [TypeLexer::T_PSEUDO_TYPE]],
            'callback as a pseudo-type' => ['callback', [TypeLexer::T_PSEUDO_TYPE]],
            '$this as a pseudo-type' => ['$this', [TypeLexer::T_PSEUDO_TYPE]],
            'static as a pseudo-type' => ['static', [TypeLexer::T_PSEUDO_TYPE]],
            'parent as a pseudo-type' => ['parent', [TypeLexer::T_PSEUDO_TYPE]],
            'real as a pseudo-type' => ['real', [TypeLexer::T_PSEUDO_TYPE]],
            'double as a pseudo-type' => ['double', [TypeLexer::T_PSEUDO_TYPE]],

            'a qualified name' => ['Qualified\\Name', [TypeLexer::T_QUALIFIED_NAME]],
            'a fully qualified name' => ['\Fully\Qualified\Name', [TypeLexer::T_FULLY_QUALIFIED_NAME]],
            'a fully qualified name with a special character' => ['\Fully\Qüalified\Name', [TypeLexer::T_FULLY_QUALIFIED_NAME]],

            'a primitive with the nullable operator' => ['?string', [TypeLexer::T_NULLABLE_OPERATOR, TypeLexer::T_PRIMITIVE_TYPE ]],
            'a compound type with null' => ['string|null', [TypeLexer::T_PRIMITIVE_TYPE, TypeLexer::T_COMPOUND_OPERATOR, TypeLexer::T_NULL, ]],
            'a compound type' => ['string|int', [TypeLexer::T_PRIMITIVE_TYPE, TypeLexer::T_COMPOUND_OPERATOR, TypeLexer::T_PSEUDO_TYPE, ]],
            'an intersection' => ['string&integer', [TypeLexer::T_PRIMITIVE_TYPE, TypeLexer::T_INTERSECTION_OPERATOR, TypeLexer::T_PRIMITIVE_TYPE, ]],
            'a primitive array' => ['string[]', [TypeLexer::T_PRIMITIVE_TYPE, TypeLexer::T_OPEN_SQUARE_BRACKET, TypeLexer::T_CLOSE_SQUARE_BRACKET, ]],
            'a complex expression with parenthesis' => [
                '(string|int)[]',
                [
                    TypeLexer::T_OPEN_PARENTHESIS,
                    TypeLexer::T_PRIMITIVE_TYPE,
                    TypeLexer::T_COMPOUND_OPERATOR,
                    TypeLexer::T_PSEUDO_TYPE,
                    TypeLexer::T_CLOSE_PARENTHESIS,
                    TypeLexer::T_OPEN_SQUARE_BRACKET,
                    TypeLexer::T_CLOSE_SQUARE_BRACKET,
                ]
            ],
            'a generic-style iterable' => [
                'iterable<integer, string>',
                [
                    TypeLexer::T_COLLECTION_TYPE,
                    TypeLexer::T_LESS_THAN,
                    TypeLexer::T_PRIMITIVE_TYPE,
                    TypeLexer::T_COMMA,
                    TypeLexer::T_PRIMITIVE_TYPE,
                    TypeLexer::T_GREATER_THAN,
                ]
            ],
        ];
    }
}
