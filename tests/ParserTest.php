<?php
namespace Kentin\Tests\TJSON;

use Kentin\TJSON\Parser;
use Kentin\TJSON\MalformedTjsonException;
use DateTime;
use GMP;

class ParserTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $parser = new Parser;
        $this->assertInstanceOf(
            Parser::class,
            $parser,
            'It should be initializable'
        );
    }

    /**
     * @dataProvider invalidTjsonProvider
     */
    public function testInvalidTjson(string $invalidTjson)
    {
        $parser = new Parser;

        $this->expectException(MalformedTjsonException::class);
        $parser->parse($invalidTjson);
    }

    public function invalidTjsonProvider()
    {
        return [
            // JSON errors
            ['[]'],
            ['{\'key\': \'value\'}'],
            ['{key: value}'],
            ['{"key": "value",}'],
            ['{"key:A<s>": ["value",]}'],
            ['{"key:S<s>": ["value",]}'],

            // TJSON errors
            ['{"key:A<>": ["value"]}'],
            ['{"key:A<f>": ["value"]}'],
            ['{"key:A<A<>>": ["value"]}'],
            ['{"key:S<>": ["value"]}'],
            ['{"key:S<f>": ["value"]}'],
            ['{"key:S<A<>>": ["value"]}'],
            ['{"key:O<>": {"value"}}'],
            ['{"key:O<f>": {"value"}}'],
            ['{"key:O<A<>>": {"value"}}'],
            ['{"key:O": {"key:s": "abc",}}'],
        ];
    }

    /**
     * @dataProvider validTjsonProvider
     */
    public function testValidTjson(string $validTjson)
    {
        $parser = new Parser;
        $this->assertInternalType(
            'array',
            $parser->parse($validTjson)
        );
    }

    public function validTjsonProvider()
    {
        return [
            ['{}'],
            ['{"key:s": "value"}'],
            ['{"key:u": "123"}'],
            ['{"key:A<>": []}'],
            ['{"key:S<>": []}'],
            ['{"key:O": {}}'],
            ['{"key:A<s>": ["abc"]}'],
            ['{"key:S<s>": ["abc"]}'],
            ['{"key:O": {"key:s": "abc"}}'],
            ['{"key:b": true}'],
            ['{"key:b": false}'],
        ];
    }

    /**
     * @dataProvider outputValidTjson
     */
    public function testOutputValidTjson(string $validTjson, array $expected)
    {
        $parser = new Parser;
        $this->assertEquals(
            $expected,
            $parser->parse($validTjson)
        );
    }

    public function outputValidTjson()
    {
        $tjson1 =
        '{
            "array-example:A<O>": [
                {
                    "string-example:s": "foobar",
                    "binary-data-example:d": "QklOQVJZ",
                    "float-example:f": 0.42,
                    "int-example:i": "42",
                    "timestamp-example:t": "2016-11-06T22:27:34Z",
                    "boolean-example:b": true
                }
            ],
            "set-example:S<i>": ["1", "2", "3"]
        }';
        $expected1 = [
            'array-example' => [
                [
                    'string-example' => 'foobar',
                    'binary-data-example' => 'BINARY',
                    'float-example' => 0.42,
                    'int-example' => new GMP('42'),
                    'timestamp-example' => new DateTime('2016-11-06T22:27:34Z'),
                    'boolean-example' => true,
                ],
            ],
            'set-example' => [
                new GMP('1'),
                new GMP('2'),
                new GMP('3'),
            ],
        ];

        return [
            [$tjson1, $expected1],
        ];
    }
}
