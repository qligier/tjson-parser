<?php

namespace Kentin\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;

class Boolean implements ScalarType
{
    /**
     * @param string $bytes
     *
     * @throws MalformedTjsonException If $bytes is not 'false' or 'true'
     *
     * @return bool
     */
    public function transform(string $bytes): bool
    {
        if ('true' === $bytes) {
            return true;
        }
        if ('false' === $bytes) {
            return false;
        }

        throw new MalformedTjsonException('Invalid Boolean');
    }
}
