<?php
namespace Kentin\Tests\TJSON;

use Kentin\TJSON\Token;
use Kentin\TJSON\TokenList;
use Kentin\TJSON\MalformedTjsonException;

class TokenListTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf(
            TokenList::class,
            new TokenList([]),
            'It should be initializable'
        );
    }

    /**
     * @dataProvider tokenProvider
     */
    public function testArrayAccessOffsetGet(Token $tokenA, Token $tokenB, Token $tokenC)
    {
        $tokenList = new TokenList([$tokenA, $tokenA, $tokenB, $tokenA, $tokenC]);

        $this->assertSame(
            $tokenA,
            $tokenList[0],
            'It should be accessible as an array'
        );
        $this->assertSame(
            $tokenB,
            $tokenList[2],
            'It should be accessible as an array'
        );
        $this->assertSame(
            $tokenC,
            $tokenList[4],
            'It should be accessible as an array'
        );
    }

    /**
     * @dataProvider tokenProvider
     */
    public function testArrayAccessOffsetSet(Token $tokenA, Token $tokenB, Token $tokenC)
    {
        $tokenList = new TokenList([$tokenA, $tokenA, $tokenA]);
        $tokenList[1] = $tokenB;
        $tokenList[2] = $tokenC;

        $this->assertSame(
            [$tokenA, $tokenB, $tokenC],
            $tokenList->getTokens(),
            'It should be assignable as an array'
        );
    }

    /**
     * @dataProvider tokenProvider
     */
    public function testArrayAccessOffsetExists(Token $tokenA, Token $tokenB, Token $tokenC)
    {
        $tokenList = new TokenList([$tokenA, $tokenA, $tokenA]);

        $this->assertSame(
            true,
            isset($tokenList[0]),
            'It should be usable with isset()'
        );
        $this->assertSame(
            true,
            isset($tokenList[2]),
            'It should be usable with isset()'
        );
        $this->assertSame(
            false,
            isset($tokenList[3]),
            'It should be usable with isset()'
        );
        $this->assertSame(
            false,
            isset($tokenList[-1]),
            'It should be usable with isset()'
        );
    }

    /**
     * @dataProvider tokenProvider
     */
    public function testArrayAccessOffsetUnset(Token $tokenA, Token $tokenB, Token $tokenC)
    {
        $tokenList = new TokenList([$tokenA, $tokenB, $tokenA]);
        unset($tokenList[1]);
        $this->assertSame(
            [$tokenA, $tokenA],
            $tokenList->getTokens(),
            'It should be usable with unset()'
        );
    }

    /**
     * @dataProvider tokenProvider
     */
    public function testIterator(Token $tokenA, Token $tokenB, Token $tokenC)
    {
        $tokenList = new TokenList([$tokenA, $tokenB, $tokenC]);

        $this->assertSame(
            $tokenA,
            $tokenList->current(),
            'It should be an iterator'
        );
        $this->assertSame(
            0,
            $tokenList->key(),
            'It should be an iterator'
        );
        $this->assertSame(
            true,
            $tokenList->valid(),
            'It should be an iterator'
        );

        $tokenList->next();

        $this->assertSame(
            $tokenB,
            $tokenList->current(),
            'It should be an iterator'
        );
        $this->assertSame(
            1,
            $tokenList->key(),
            'It should be an iterator'
        );
        $this->assertSame(
            true,
            $tokenList->valid(),
            'It should be an iterator'
        );

        $tokenList->next();

        $this->assertSame(
            $tokenC,
            $tokenList->current(),
            'It should be an iterator'
        );
        $this->assertSame(
            2,
            $tokenList->key(),
            'It should be an iterator'
        );
        $this->assertSame(
            true,
            $tokenList->valid(),
            'It should be an iterator'
        );

        $tokenList->next();

        $this->assertSame(
            3,
            $tokenList->key(),
            'It should be an iterator'
        );
        $this->assertSame(
            false,
            $tokenList->valid(),
            'It should be an iterator'
        );

        $tokenList->rewind();

        $this->assertSame(
            $tokenA,
            $tokenList->current(),
            'It should be an iterator'
        );
        $this->assertSame(
            0,
            $tokenList->key(),
            'It should be an iterator'
        );
        $this->assertSame(
            true,
            $tokenList->valid(),
            'It should be an iterator'
        );
    }

    /**
     * @dataProvider tokenProvider
     */
    public function testIteratorOutOfBoundAccess(Token $tokenA, Token $tokenB, Token $tokenC)
    {
        $tokenList = new TokenList([$tokenA, $tokenB]);
        $tokenList->next();
        $tokenList->next();

        $this->expectException(MalformedTjsonException::class);
        $tokenList->current();
    }


    public function tokenProvider()
    {
        return [[
            new Token(['A', 1, 'A']),
            new Token(['B', 1, 'B']),
            new Token(['C', 1, 'C']),
        ]];
    }
}
