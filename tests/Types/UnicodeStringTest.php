<?php
namespace Kentin\Tests\TJSON\Types;

use Kentin\TJSON\Types\UnicodeString;
use Kentin\TJSON\MalformedTjsonException;

class UnicodeStringTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $type = new UnicodeString;

        $this->assertInstanceOf(
            UnicodeString::class,
            $type,
            'It should be initializable'
        );
    }

    /**
     * @dataProvider unicodeSequenceProvider
     */
    public function testDecodeUnicodeSequence(string $sequence, string $expected)
    {
        $type = new UnicodeString;
        $this->assertSame(
            $expected,
            $type->decodeUnicodeSequence($sequence),
            'It should decode unicode sequences'
        );
    }

    public function unicodeSequenceProvider()
    {
        return [
            ['00e9', 'é'],
            ['4f60', '你'],
            ['672c', '本'],
            ['26c4', '⛄'],
            ['0b87', 'இ'],
        ];
    }

    /**
     * @dataProvider escapedCharProvider
     */
    public function testDecodeEscapedChar(string $escapedChar, string $expected)
    {
        $type = new UnicodeString;
        $this->assertSame(
            $expected,
            $type->decodeEscapedChar($escapedChar),
            'It should decode escaped char'
        );
    }

    public function escapedCharProvider()
    {
        return [
            ['"', '"'],
            ['\\', '\\'],
            ['/', '/'],
            ['b', "\x8"],
            ['f', "\f"],
            ['n', "\n"],
            ['r', "\r"],
            ['t', "\t"],
        ];
    }

    /**
     * @dataProvider stringProvider
     */
    public function testDecodeJsonString(string $jsonString, string $expected)
    {
        $type = new UnicodeString;
        $this->assertSame(
            $expected,
            $type->decodeJsonString($jsonString),
            'It should decode json string'
        );
    }

    /**
     * @dataProvider stringProvider
     */
    public function testTransform(string $jsonString, string $expected)
    {
        $type = new UnicodeString;
        $this->assertSame(
            $expected,
            $type->transform('"'.$jsonString.'"'),
            'It should decode json string'
        );
    }

    public function stringProvider()
    {
        return [
            ['abc', 'abc'],
            ['hello\\tworld\\n', "hello\tworld\n"],
            ['\\u00e9 \\u4f60 \\u672c', 'é 你 本']
        ];
    }
}
