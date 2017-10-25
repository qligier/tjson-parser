<?php
namespace Kentin\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;

class Base16 implements ScalarType
{
    /**
     * Regex that match the valid formats of base16 strings
     *
     * @var string
     */
    const FORMAT_REGEX = '~[a-f0-9]*$~A';

    /**
     * @param string $bytes
     *
     * @return string
     * @throws MalformedTjsonException
     */
    public function transform(string $bytes): string
    {
        if (!\preg_match(self::FORMAT_REGEX, $bytes)) {
            throw new MalformedTjsonException('Invalid Base16 format');
        }

        return $this->base16Decode($bytes);
    }

    /**
     * Decode a base16-encoded string
     *
     * @param string $base16
     *
     * @return string
     */
    public function base16Decode(string $base16): string
    {
        return pack('H*', $base16);
    }
}
