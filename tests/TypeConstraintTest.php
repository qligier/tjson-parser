<?php

namespace Kentin\Tests\TJSON;

use Kentin\TJSON\MalformedTjsonException;
use Kentin\TJSON\TypeConstraint;

class TypeConstraintTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf(
            TypeConstraint::class,
            new TypeConstraint('b'),
            'It should be initializable'
        );
    }

    public function testTypeBoolean()
    {
        $this->assertSame(
            'b',
            (new TypeConstraint('b'))->getType(),
            'It should accept a boolean'
        );
        $this->assertSame(
            null,
            (new TypeConstraint('b'))->getInnerConstraint(),
            'It should not have an inner type'
        );
    }

    public function testTypeSignedInteger()
    {
        $this->assertSame(
            'i',
            (new TypeConstraint('i'))->getType(),
            'It should accept a SignedInteger'
        );
    }

    public function testTypeUnsignedInteger()
    {
        $this->assertSame(
            'u',
            (new TypeConstraint('u'))->getType(),
            'It should accept a UnsignedInteger'
        );
    }

    public function testTypeFloat()
    {
        $this->assertSame(
            'f',
            (new TypeConstraint('f'))->getType(),
            'It should accept a float'
        );
    }

    public function testTypeString()
    {
        $this->assertSame(
            's',
            (new TypeConstraint('s'))->getType(),
            'It should accept a string'
        );
    }

    public function testTypeTimestamp()
    {
        $this->assertSame(
            't',
            (new TypeConstraint('t'))->getType(),
            'It should accept a timestamp'
        );
    }

    public function testTypeBase16()
    {
        $this->assertSame(
            'd16',
            (new TypeConstraint('d16'))->getType(),
            'It should accept a base16'
        );
    }

    public function testTypeBase32()
    {
        $this->assertSame(
            'd32',
            (new TypeConstraint('d32'))->getType(),
            'It should accept a base32'
        );
    }

    public function testTypeBase64()
    {
        $this->assertSame(
            'd64',
            (new TypeConstraint('d64'))->getType(),
            'It should accept a base64url'
        );
        $this->assertSame(
            'd64',
            (new TypeConstraint('d'))->getType(),
            'It should accept a base64url'
        );
    }

    public function testTypeObject()
    {
        $this->assertSame(
            'O',
            (new TypeConstraint('O'))->getType(),
            'It should accept an object'
        );
    }

    public function testTypeArray()
    {
        $this->assertSame(
            'A',
            (new TypeConstraint('A<>'))->getType(),
            'It should accept an array without an inner type'
        );
        $this->assertSame(
            null,
            (new TypeConstraint('A<>'))->getInnerConstraint(),
            'It should not have an inner type'
        );

        $this->assertSame(
            'A',
            (new TypeConstraint('A<i>'))->getType(),
            'It should accept an array with an inner type'
        );
        $this->assertSame(
            'i',
            (new TypeConstraint('A<i>'))->getInnerConstraint()->getType(),
            'It should have an inner type'
        );
    }

    public function testTypeSet()
    {
        $this->assertSame(
            'S',
            (new TypeConstraint('S<>'))->getType(),
            'It should accept an set without an inner type'
        );
        $this->assertSame(
            null,
            (new TypeConstraint('S<>'))->getInnerConstraint(),
            'It should not have an inner type'
        );

        $this->assertSame(
            'S',
            (new TypeConstraint('S<u>'))->getType(),
            'It should accept an set with an inner type'
        );
        $this->assertSame(
            'u',
            (new TypeConstraint('S<u>'))->getInnerConstraint()->getType(),
            'It should have an inner type'
        );
    }

    public function testUnknownType()
    {
        $this->expectException(MalformedTjsonException::class);
        new TypeConstraint('W');
    }

    public function testInnerType()
    {
        $this->expectException(MalformedTjsonException::class);
        new TypeConstraint('u<i>');
    }
}
