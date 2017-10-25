<?php

namespace Kentin\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;

class SignedInteger implements ScalarType
{
    /**
     * Regex that match the valid formats of signed integers.
     *
     * @var string
     */
    const FORMAT_REGEX = '~-?(?:0|[1-9]\d*)(?:[eE][+-]?\d+)?$~A';

    /**
     * Smallest valid signed integer per TJSON spec.
     *
     * @var string
     */
    const MIN_INTEGER = '-9223372036854775808';

    /**
     * Biggest valid signed integer per TJSON spec.
     *
     * @var string
     */
    const MAX_INTEGER = '9223372036854775807';

    /**
     * @param string $bytes
     *
     * @throws MalformedTjsonException
     *
     * @return \GMP
     */
    public function transform(string $bytes): \GMP
    {
        if (!\preg_match(self::FORMAT_REGEX, $bytes)) {
            throw new MalformedTjsonException('Invalid SignedInteger format');
        }

        $signedInteger = \gmp_init($bytes, 10);
        $minInteger = \gmp_init(self::MIN_INTEGER, 10);
        $maxInteger = \gmp_init(self::MAX_INTEGER, 10);

        if (\gmp_cmp($signedInteger, $maxInteger) > 0) {
            throw new MalformedTjsonException('SignedInteger is bigger than (2**63)-1');
        }
        if (\gmp_cmp($signedInteger, $minInteger) < 0) {
            throw new MalformedTjsonException('SignedInteger is smaller than -(2**63)');
        }

        return $signedInteger;
    }
}
