<?php
namespace Kentin\Tests\TJSON;

use Kentin\TJSON\LexerFactory;
use Kentin\TJSON\Tokens;
use Phlexy\LexingException;

class LexerTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $lexer = (new LexerFactory)->createLexer();
        $this->assertInstanceOf(
            \Phlexy\Lexer::class,
            $lexer,
            'It should be initializable'
        );
    }

    public function testLexStrings()
    {
        $lexer = (new LexerFactory)->createLexer();

        $this->assertSameTokens(
            [Tokens::T_JSON_STRING],
            $lexer->lex('""'),
            'It shoud lex empty string'
        );

        $this->assertSameTokens(
            [Tokens::T_JSON_STRING],
            $lexer->lex('"abc"'),
            'It shoud lex string'
        );

        $this->assertSame(
            [[Tokens::T_JSON_STRING, 1, '"abc"']],
            $lexer->lex('"abc"'),
            'It shoud return the full string'
        );

        $this->assertSameTokens(
            [Tokens::T_JSON_STRING],
            $lexer->lex('"abc\\"def"'),
            'It shoud lex string with escaped quote'
        );

        $this->assertSameTokens(
            [Tokens::T_JSON_STRING],
            $lexer->lex('" \\" \\\\ \\n \\u \\t \\u1234 "'),
            'It shoud lex string with escaped chars'
        );

        $this->assertSameTokens(
            [Tokens::T_JSON_STRING],
            $lexer->lex('" éàè)=(&½@#|} "'),
            'It shoud lex string'
        );
    }

    public function testLexCurlyBracket()
    {
        $lexer = (new LexerFactory)->createLexer();

        $this->assertSameTokens(
            [Tokens::T_JSON_LEFT_CURLY_BRACKET],
            $lexer->lex('{'),
            'It shoud lex left curly bracket'
        );

        $this->assertSameTokens(
            [Tokens::T_JSON_RIGHT_CURLY_BRACKET],
            $lexer->lex('}'),
            'It shoud lex right curly bracket'
        );
    }

    public function testLexSquareBracket()
    {
        $lexer = (new LexerFactory)->createLexer();

        $this->assertSameTokens(
            [Tokens::T_JSON_LEFT_SQUARE_BRACKET],
            $lexer->lex('['),
            'It shoud lex left square bracket'
        );

        $this->assertSameTokens(
            [Tokens::T_JSON_RIGHT_SQUARE_BRACKET],
            $lexer->lex(']'),
            'It shoud lex right square bracket'
        );
    }

    public function testLexWhitespaces()
    {
        $lexer = (new LexerFactory)->createLexer();

        $this->assertSameTokens(
            [Tokens::T_JSON_WHITESPACE],
            $lexer->lex(" \n\r\t"),
            'It shoud lex whitespaces'
        );
    }

    public function testLexComma()
    {
        $lexer = (new LexerFactory)->createLexer();

        $this->assertSameTokens(
            [Tokens::T_JSON_COMMA],
            $lexer->lex(','),
            'It shoud lex comma'
        );
    }

    public function testLexColon()
    {
        $lexer = (new LexerFactory)->createLexer();

        $this->assertSameTokens(
            [Tokens::T_JSON_COLON],
            $lexer->lex(':'),
            'It shoud lex colon'
        );
    }

    public function testLexBoolean()
    {
        $lexer = (new LexerFactory)->createLexer();

        $this->assertSameTokens(
            [Tokens::T_JSON_FALSE],
            $lexer->lex('false'),
            'It shoud lex false'
        );

        $this->assertSameTokens(
            [Tokens::T_JSON_TRUE],
            $lexer->lex('true'),
            'It shoud lex true'
        );
    }

    public function testLexNull()
    {
        $lexer = (new LexerFactory)->createLexer();

        $this->assertSameTokens(
            [Tokens::T_JSON_NULL],
            $lexer->lex('null'),
            'It shoud lex null'
        );
    }

    public function testLexUppercaseFalse()
    {
        $lexer = (new LexerFactory)->createLexer();
        $this->expectException(LexingException::class);
        $lexer->lex('FALSE');
    }

    public function testLexUppercaseTrue()
    {
        $lexer = (new LexerFactory)->createLexer();
        $this->expectException(LexingException::class);
        $lexer->lex('TRUE');
    }

    public function testLexUppercaseNull()
    {
        $lexer = (new LexerFactory)->createLexer();
        $this->expectException(LexingException::class);
        $lexer->lex('NULL');
    }

    /**
     * @dataProvider validNumberProvider
     */
    public function testLexNumber(string $number)
    {
        $lexer = (new LexerFactory)->createLexer();
        $this->assertSameTokens(
            [Tokens::T_JSON_NUMBER],
            $lexer->lex($number),
            'It shoud lex '.$number
        );
    }

    public function validNumberProvider()
    {
        return [
            'zero' => ['0'],
            'negative zero' => ['-0'],
            'positive number' => ['42'],
            'negative number' => ['-13'],
            'number with exponant' => ['-1e+1'],
            'positive float' => ['0.1'],
            'negative float' => ['-5.97'],
            'positive float with exponant' => ['0.1e2'],
            'negative float with exponant' => ['-5.97e-4'],
        ];
    }

    public function testLexJson()
    {
        $lexer = (new LexerFactory)->createLexer();

        $this->assertSameTokens(
            [
                Tokens::T_JSON_LEFT_CURLY_BRACKET,
                Tokens::T_JSON_STRING,
                Tokens::T_JSON_COLON,
                Tokens::T_JSON_WHITESPACE,
                Tokens::T_JSON_NUMBER,
                Tokens::T_JSON_WHITESPACE,
                Tokens::T_JSON_RIGHT_CURLY_BRACKET,
            ],
            $lexer->lex('{"key": 123 }'),
            'It shoud lex a JSON string'
        );
    }

    private function assertSameTokens(array $expected, array $actual, string $message = '')
    {
        $actual = $this->reduceTokens($actual);
        $this->assertSame($expected, $actual, $message);
    }

    private function assertNotSameTokens(array $expected, array $actual, string $message = '')
    {
        $actual = $this->reduceTokens($actual);
        $this->assertNotSame($expected, $actual, $message);
    }

    private function reduceTokens(array $fullTokens)
    {
        $reducedTokens = [];
        foreach ($fullTokens as $token) {
            $reducedTokens[] = $token[0];
        }
        return $reducedTokens;
    }
}
