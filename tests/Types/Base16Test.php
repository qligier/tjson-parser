<?php

namespace Kentin\Tests\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;
use Kentin\TJSON\Types\Base16;

class Base16Test extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $type = new Base16();

        $this->assertInstanceOf(
            Base16::class,
            $type,
            'It should be initializable'
        );
    }

    public function testTransformEmptyString()
    {
        $type = new Base16();

        $this->assertSame(
            '',
            $type->transform(''),
            'It should transform an empty string'
        );
    }

    public function testTransformValidStrings()
    {
        $type = new Base16();

        $this->assertSame(
            'Hello, world!',
            $type->transform('48656c6c6f2c20776f726c6421'),
            'It should transform a valid string'
        );
    }

    public function testTransformAccents()
    {
        $type = new Base16();

        $this->assertSame(
            'çéàè',
            $type->transform('c3a7c3a9c3a0c3a8'),
            'It should transform accents'
        );
    }

    public function testTransformAllAlphabet()
    {
        $type = new Base16();

        $this->assertSame(
            'ABCDEFGHIJKLMNOP',
            $type->transform('4142434445464748494a4b4c4d4e4f50'),
            'It shoud transform all base16 alphabet'
        );
    }

    public function testTransformUppercaseAlphabet()
    {
        $type = new Base16();

        $this->expectException(MalformedTjsonException::class);
        $type->transform(
            '48656C6C6F2C20776F726C6421'
        );
    }

    public function testTransformInvalidAlphabet()
    {
        $type = new Base16();

        $this->expectException(MalformedTjsonException::class);
        $type->transform(
            'abcio'
        );
    }
}
