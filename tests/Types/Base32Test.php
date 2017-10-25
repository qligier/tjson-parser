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

    /**
     * @dataProvider transformValidStringsProvider
     */
    public function testTransformValidStrings(string $base32, string $expected)
    {
        $type = new Base32();

        $this->assertSame(
            $expected,
            $type->transform($base32),
            'It should transform a valid string'
        );
    }

    public function transformValidStringsProvider(): array
    {
        return [
            ['jbswy3dpfqqfo33snrscc', 'Hello, World!'],
            ['mfqwcytcmjrwgy3emrsgkzlfmztgmz3hm4', 'aaabbbcccdddeeefffggg'],
            ['nbugq2ljnfvgu2tlnnvwy3dmnvww23tonzxw63y', 'hhhiiijjjkkklllmmmnnnooo'],
            ['na', 'h'],
            ['pi', 'z'],
            ['f7', '/'],
            ['me', 'a'],
            ['mfqq', 'aa'],
            ['mfqwc', 'aaa'],
            ['mfqwcyi', 'aaaa'],
            ['mfqwcylb', 'aaaaa'],
            ['mfqwcylbme', 'aaaaaa'],
        ];
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
