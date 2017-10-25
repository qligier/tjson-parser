<?php

namespace Kentin\Tests\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;
use Kentin\TJSON\Types\Boolean;

class BooleanTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $type = new Boolean();

        $this->assertInstanceOf(Boolean::class, $type, 'It should be initializable');
    }

    public function testReturnValue()
    {
        $type = new Boolean();

        $this->assertSame(
            false,
            $type->transform('false'),
            'It should transform false'
        );

        $this->assertSame(
            true,
            $type->transform('true'),
            'It should transform true'
        );
    }

    public function testInvalidValue()
    {
        $type = new Boolean();

        $this->expectException(MalformedTjsonException::class);
        $type->transform('hello');
    }
}
