<?php
namespace Kentin\Tests\TJSON\Types;

use Kentin\TJSON\Types\SignedInteger;
use Kentin\TJSON\MalformedTjsonException;

class SignedIntegerTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $type = new SignedInteger;

        $this->assertInstanceOf(SignedInteger::class, $type, 'It should be initializable');
    }

    public function testTransformValidInt()
    {
        $type = new SignedInteger;

        $this->assertEquals(
            gmp_init('12'),
            $type->transform('12'),
            'It should transform valid signed integer'
        );
        
        $this->assertEquals(
            gmp_init('-37'),
            $type->transform('-37'),
            'It should transform valid signed integer'
        );
        
        $this->assertEquals(
            gmp_init('0'),
            $type->transform('0'),
            'It should transform valid signed integer'
        );
        
        $this->assertEquals(
            gmp_init('-0'),
            $type->transform('-0'),
            'It should transform valid signed integer'
        );
        
        $this->assertEquals(
            gmp_init('9223372036854775807'),
            $type->transform('9223372036854775807'),
            'It should transform valid signed integer'
        );
        
        $this->assertEquals(
            gmp_init('-9223372036854775808'),
            $type->transform('-9223372036854775808'),
            'It should transform valid signed integer'
        );
        /*
        $this->assertEquals(
            gmp_init('2000'),
            $type->transform('2e3'),
            'It should transform valid signed integer'
        );
        */
    }

    public function testTransformOverflow()
    {
        $type = new SignedInteger;

        $this->expectException(MalformedTjsonException::class);
        $this->expectExceptionMessage('SignedInteger is bigger than (2**63)-1');
        $type->transform('9223372036854775808');
    }
    
    public function testTransformUnderflow()
    {
        $type = new SignedInteger;

        $this->expectException(MalformedTjsonException::class);
        $this->expectExceptionMessage('SignedInteger is smaller than -(2**63)');
        $type->transform('-9223372036854775809');
    }
}
