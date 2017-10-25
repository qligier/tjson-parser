<?php
namespace Kentin\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;

class UnsignedInteger implements ScalarType
{
    /**
     * Regex that match the valid formats of signed integers
     *
     * @var string
     */
    const FORMAT_REGEX = '~(?:0|[1-9]\d*)(?:[eE][+-]?\d+)?$~A';

    /**
     * Biggest valid unsigned integer per TJSON spec
     *
     * @var string
     */
    const MAX_INTEGER = '18446744073709551615';

    /**
     * @param string $bytes
     *
     * @return \GMP
     * @throws MalformedTjsonException
     */
    public function transform(string $bytes): \GMP
    {
        if (!\preg_match(self::FORMAT_REGEX, $bytes)) {
            throw new MalformedTjsonException('Invalid UnsignedInteger format');
        }

        $signedInteger = \gmp_init($bytes, 10);
        $maxInteger = \gmp_init(self::MAX_INTEGER, 10);

        if (\gmp_cmp($signedInteger, $maxInteger) > 0) {
            throw new MalformedTjsonException('UnsignedInteger is bigger than (2**64)-1');
        }

        return $signedInteger;
    }
}
