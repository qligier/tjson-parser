<?php

namespace Kentin\TJSON;

class LexerFactory
{
    /**
     * Create a JSON lexer.
     *
     * @return \Phlexy\Lexer
     */
    public function createLexer(): \Phlexy\Lexer
    {
        $factory = new \Phlexy\LexerFactory\Stateless\UsingPregReplace(
            new \Phlexy\LexerDataGenerator()
        );

        return $factory->createLexer([
            '\{'                                          => Tokens::T_JSON_LEFT_CURLY_BRACKET,
            '\}'                                          => Tokens::T_JSON_RIGHT_CURLY_BRACKET,
            '\['                                          => Tokens::T_JSON_LEFT_SQUARE_BRACKET,
            '\]'                                          => Tokens::T_JSON_RIGHT_SQUARE_BRACKET,
            '(?: |\n|\r|\t)+'                             => Tokens::T_JSON_WHITESPACE,
            ':'                                           => Tokens::T_JSON_COLON,
            ','                                           => Tokens::T_JSON_COMMA,
            'true'                                        => Tokens::T_JSON_TRUE,
            'false'                                       => Tokens::T_JSON_FALSE,
            'null'                                        => Tokens::T_JSON_NULL,
            '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"'              => Tokens::T_JSON_STRING,
            '-?(?:0|[1-9]\d*)(?:\.\d+)?(?:[eE][+-]?\d+)?' => Tokens::T_JSON_NUMBER,
        ]);
    }
}
