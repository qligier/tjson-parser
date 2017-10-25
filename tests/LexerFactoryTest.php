<?php

namespace Kentin\Tests\TJSON;

use Kentin\TJSON\LexerFactory;

class LexerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $factory = new LexerFactory();
        $this->assertInstanceOf(
            LexerFactory::class,
            $factory,
            'It should be initializable'
        );
    }

    public function testCreateLexer()
    {
        $factory = new LexerFactory();
        $lexer = $factory->createLexer();
        $this->assertInstanceOf(
            \Phlexy\Lexer::class,
            $lexer,
            'It should return a lexer'
        );
    }
}
