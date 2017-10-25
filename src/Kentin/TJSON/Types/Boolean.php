<?php
namespace Kentin\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;

class Boolean implements ScalarType
{
    /**
     * @param string $bytes
     *
     * @return bool
     * @throws MalformedTjsonException If $bytes is not 'false' or 'true'
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
