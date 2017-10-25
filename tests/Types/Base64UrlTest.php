<?php

namespace Kentin\Tests\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;
use Kentin\TJSON\Types\Base64Url;

class Base64UrlTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $type = new Base64Url();

        $this->assertInstanceOf(Base64Url::class, $type, 'It should be initializable');
    }

    public function testTransformEmptyString()
    {
        $type = new Base64Url();

        $this->assertSame(
            '',
            $type->transform(''),
            'It should transform an empty string'
        );
    }

    public function testTransformValidStrings()
    {
        $type = new Base64Url();

        $this->assertSame(
            'Hello, World!',
            $type->transform('SGVsbG8sIFdvcmxkIQ'),
            'It should transform a valid string'
        );

        $this->assertSame(
            '?)ç/~¢~@¦°~¦°',
            $type->transform('PynDpy9-wqJ-QMKmwrB-wqbCsA'),
            'It should transform a valid string'
        );

        $this->assertSame(
            'çéàè',
            $type->transform('w6fDqcOgw6g'),
            'It should transform a valid string'
        );
    }

    public function testTransformInvalidAlphabet()
    {
        $type = new Base64Url();

        $this->expectException(MalformedTjsonException::class);
        $type->transform(
            'çaak'
        );
    }

    public function testTransformBase64()
    {
        $type = new Base64Url();

        $this->expectException(MalformedTjsonException::class);
        $type->transform(
            'PynDpy9+wqJ+QMKmwrB+wqbCsA'
        );
    }

    public function testPadding()
    {
        $type = new Base64Url();

        $this->expectException(MalformedTjsonException::class);
        $type->transform(
            'w6fDqcOgw6g='
        );
    }

    public function testAlphabetTranslation()
    {
        $type = new Base64Url();

        $this->assertSame(
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/',
            $type->translateToBase64('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_'),
            'It should translate base64 alphabet'
        );
    }
}
