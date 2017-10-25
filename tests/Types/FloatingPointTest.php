<?php

namespace Kentin\Tests\TJSON\Types;

use Kentin\TJSON\Types\FloatingPoint;

class FloatingPointTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $type = new FloatingPoint();

        $this->assertInstanceOf(FloatingPoint::class, $type, 'It should be initializable');
    }

    public function testReturnValue()
    {
        $type = new FloatingPoint();

        $this->assertSame(
            0.1,
            $type->transform('0.1'),
            'It should transform 0.1'
        );

        $this->assertSame(
            1.0,
            $type->transform('1'),
            'It should transform 1.0'
        );
    }
}
