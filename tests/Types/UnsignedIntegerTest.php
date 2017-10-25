<?php

namespace Kentin\Tests\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;
use Kentin\TJSON\Types\UnsignedInteger;

class UnsignedIntegerTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $type = new UnsignedInteger();

        $this->assertInstanceOf(UnsignedInteger::class, $type, 'It should be initializable');
    }

    public function testTransformValidInt()
    {
        $type = new UnsignedInteger();

        $this->assertEquals(
            gmp_init('42'),
            $type->transform('42'),
            'It should transform valid unsigned integer'
        );

        $this->assertEquals(
            gmp_init('18446744073709551615'),
            $type->transform('18446744073709551615'),
            'It should transform valid unsigned integer'
        );

        $this->assertEquals(
            gmp_init('0'),
            $type->transform('0'),
            'It should transform valid unsigned integer'
        );
    }

    public function testTransformOverflow()
    {
        $type = new UnsignedInteger();

        $this->expectException(MalformedTjsonException::class);
        $this->expectExceptionMessage('UnsignedInteger is bigger than (2**64)-1');
        $type->transform('18446744073709551616');
    }

    public function testInvalidFormat()
    {
        $type = new UnsignedInteger();

        $this->expectException(MalformedTjsonException::class);
        $this->expectExceptionMessage('Invalid UnsignedInteger format');
        $type->transform('abc');
    }
}
