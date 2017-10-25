<?php
namespace Kentin\Tests\TJSON;

use Kentin\TJSON\Token;

class TokenTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf(
            Token::class,
            new Token(['', 1, '']),
            'It should be initializable'
        );
    }

    public function testParameters()
    {
        $token = new Token(['This is the type', 3, 'This is the value']);

        $this->assertSame(
            'This is the value',
            $token->getValue(),
            'It should keep the value'
        );
        $this->assertSame(
            true,
            $token->is('This is the type'),
            'It should keep the type'
        );
    }

    public function testInvalidArray()
    {
        $this->expectException(\Exception::class);
        new Token(['A', 2]);
    }
}
