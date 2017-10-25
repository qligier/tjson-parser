<?php
namespace Kentin\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;

class Base32 implements ScalarType
{
    /**
     * Regex that match the valid formats of base32 strings
     *
     * @var string
     */
    const FORMAT_REGEX = '~[a-z2-7]*$~A';

    /**
     * Lower-case variant of the RFC4648 base32 alphabet
     *
     * @var array<string, int>
     */
    const BASE32_ALPHABET = [
        'a' => 0, 'b' => 1, 'c' => 2, 'd' => 3, 'e' => 4, 'f' => 5,
        'g' => 6, 'h' => 7, 'i' => 8, 'j' => 9, 'k' => 10, 'l' => 11,
        'm' => 12, 'n' => 13, 'o' => 14, 'p' => 15, 'q' => 16, 'r' => 17,
        's' => 18, 't' => 19, 'u' => 20, 'v' => 21, 'w' => 22, 'x' => 23,
        'y' => 24, 'z' => 25, '2' => 26, '3' => 27, '4' => 28, '5' => 29,
        '6' => 30, '7' => 31, '=' => 0,
    ];

    /**
     * @param string $bytes
     *
     * @return string
     */
    public function transform(string $bytes): string
    {
        if (!\preg_match(self::FORMAT_REGEX, $bytes)) {
            throw new MalformedTjsonException('Invalid Base32 format');
        }

        return $this->base32Decode($bytes);
    }

    /**
     * Decode a base32-encoded string
     * Based on skleeschulte/php-base32
     *
     * @param string $base32
     *
     * @return string
     *
     * @psalm-suppress PossiblyUndefinedVariable
     */
    public function base32Decode(string $base32): string
    {
        // Iterate over blocks of 8 characters
        $base32Length = strlen($base32);
        $decodedString = '';
        for ($i = 0; $i < $base32Length; $i = $i + 8) {
            if ($base32Length - $i >= 2) {
                $bitGroup1 = self::BASE32_ALPHABET[$base32[$i]];
                $bitGroup2 = self::BASE32_ALPHABET[$base32[$i + 1]];
                $decodedString .= chr($bitGroup1 << 3 | $bitGroup2 >> 2);
            }

            if ($base32Length - $i >= 4) {
                $bitGroup3 = self::BASE32_ALPHABET[$base32[$i + 2]];
                $bitGroup4 = self::BASE32_ALPHABET[$base32[$i + 3]];
                $decodedString .= chr(($bitGroup2 & -29) << 6 | $bitGroup3 << 1 | $bitGroup4 >> 4);
            }

            if ($base32Length - $i >= 5) {
                $bitGroup5 = self::BASE32_ALPHABET[$base32[$i + 4]];
                $decodedString .= chr(($bitGroup4 & -17) << 4 | $bitGroup5 >> 1);
            }

            if ($base32Length - $i >= 7) {
                $bitGroup6 = self::BASE32_ALPHABET[$base32[$i + 5]];
                $bitGroup7 = self::BASE32_ALPHABET[$base32[$i + 6]];
                $decodedString .= chr(($bitGroup5 & 1) << 7 | $bitGroup6 << 2 | $bitGroup7 >> 3);
            }

            if ($base32Length - $i >= 8) {
                $bitGroup8 = self::BASE32_ALPHABET[$base32[$i + 7]];
                $decodedString .= chr(($bitGroup7 & -25) << 5 | $bitGroup8);
            }
        }

        return $decodedString;
    }
}
