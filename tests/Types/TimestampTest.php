<?php
namespace Kentin\Tests\TJSON\Types;

use Kentin\TJSON\Types\Timestamp;
use Kentin\TJSON\MalformedTjsonException;
use DateTime;

class TimestampTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $type = new Timestamp;

        $this->assertInstanceOf(Timestamp::class, $type, 'It should be initializable');
    }

    public function testRejectEmptyString()
    {
        $type = new Timestamp;

        $this->expectException(MalformedTjsonException::class);
        $type->transform('');
    }

    public function testTransformValidDate()
    {
        $type = new Timestamp;

        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d\TH:i:sP', '2005-08-15T15:52:01Z'),
            $type->transform('2005-08-15T15:52:01Z'),
            'It should transform a valid date'
        );
    }

    public function testRejectTimezone()
    {
        $type = new Timestamp;
        
        $this->expectException(MalformedTjsonException::class);
        $type->transform('2005-08-15T15:52:01+00:00');
    }
}
