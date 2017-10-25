<?php

namespace Kentin\Tests\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;
use Kentin\TJSON\Types\Base32;

class Base32Test extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $type = new Base32();

        $this->assertInstanceOf(Base32::class, $type, 'It should be initializable');
    }

    public function testTransformEmptyString()
    {
        $type = new Base32();

        $this->assertSame(
            '',
            $type->transform(''),
            'It should transform an empty string'
        );

        $this->assertSame(
            '',
            $type->base32Decode(''),
            'It should transform an empty string'
        );
    }

    public function testTransformValidStrings()
    {
        $type = new Base32();

        $this->assertSame(
            'Hello, World!',
            $type->transform('jbswy3dpfqqfo33snrscc'),
            'It should transform a valid string'
        );

        $this->assertSame(
            'Hello, World!',
            $type->base32Decode('jbswy3dpfqqfo33snrscc'),
            'It should transform a valid string'
        );
    }

    public function testTransformAccents()
    {
        $type = new Base32();

        $this->assertSame(
            'çéàè',
            $type->transform('yot4hkodudb2q'),
            'It should transform accents'
        );
    }

    public function testTransformAllAlphabet()
    {
        $type = new Base32();

        $this->assertSame(
            'aaabbbcccdddeeefffggg',
            $type->transform('mfqwcytcmjrwgy3emrsgkzlfmztgmz3hm4'),
            'It shoud transform all base32 alphabet'
        );
        $this->assertSame(
            'hhhiiijjjkkklllmmmnnnooo',
            $type->transform('nbugq2ljnfvgu2tlnnvwy3dmnvww23tonzxw63y'),
            'It shoud transform all base32 alphabet'
        );
        $this->assertSame(
            'h',
            $type->transform('na'),
            'It shoud transform all base32 alphabet'
        );
        $this->assertSame(
            'z',
            $type->transform('pi'),
            'It shoud transform all base32 alphabet'
        );
        $this->assertSame(
            '/',
            $type->transform('f7'),
            'It shoud transform all base32 alphabet'
        );
    }

    public function testTransformUppercaseAlphabet()
    {
        $type = new Base32();

        $this->expectException(MalformedTjsonException::class);
        $type->transform(
            'JBSWY3DP'
        );
    }

    public function testTransformInvalidAlphabet()
    {
        $type = new Base32();

        $this->expectException(MalformedTjsonException::class);
        $type->transform(
            'a19z'
        );
    }
}
