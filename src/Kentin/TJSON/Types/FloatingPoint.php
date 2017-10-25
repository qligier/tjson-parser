<?php

namespace Kentin\TJSON\Types;

class FloatingPoint implements ScalarType
{
    /**
     * @param string $bytes
     *
     * @return float
     */
    public function transform(string $bytes): float
    {
        return (float) $bytes;
    }
}
