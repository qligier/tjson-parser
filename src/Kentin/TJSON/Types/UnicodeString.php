<?php

namespace Kentin\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;

class UnicodeString implements ScalarType
{
    /**
     * Decode the string token literal value.
     *
     * @param string $bytes
     *
     * @return string
     */
    public function transform(string $bytes): string
    {
        // Remove double quotes
        $jsonString = substr($bytes, 1, -1);

        return $this->decodeJsonString($jsonString);
    }

    /**
     * Decode a JSON-encoded string.
     *
     * @param string $jsonString
     *
     * @throws MalformedTjsonException
     *
     * @return string
     */
    public function decodeJsonString(string $jsonString): string
    {
        $decodedString = '';
        $chars = str_split($jsonString, 1);
        $length = count($chars);
        for ($i = 0; $i < $length; ++$i) {
            if ('\\' === $chars[$i]) {
                if ('u' === $chars[$i + 1]) {
                    if ($length - $i < 6) {
                        throw new MalformedTjsonException('Invalid UnicodeString format');
                    }
                    $chars[$i] = $this->decodeUnicodeSequence(
                        $chars[$i + 2].$chars[$i + 3].$chars[$i + 4].$chars[$i + 5]
                    );
                    array_splice($chars, $i + 1, 5);
                    $length = $length - 5;
                    continue;
                } else {
                    $chars[$i] = $this->decodeEscapedChar($chars[$i + 1]);
                    array_splice($chars, $i + 1, 1);
                    --$length;
                    continue;
                }
            }
        }

        return implode('', $chars);
    }

    /**
     * Decode an unicode sequence.
     *
     * @param string $unicodeSequence
     *
     * @return string
     */
    public function decodeUnicodeSequence(string $unicodeSequence): string
    {
        return mb_convert_encoding(pack('H*', $unicodeSequence), 'UTF-8', 'UCS-2BE');
    }

    /**
     * Decode a JSON escaped char.
     *
     * @param string $escapedChar
     *
     * @throws MalformedTjsonException
     *
     * @return string
     */
    public function decodeEscapedChar(string $escapedChar): string
    {
        if ('"' === $escapedChar) {
            return '"';
        }
        if ('\\' === $escapedChar) {
            return '\\';
        }
        if ('/' === $escapedChar) {
            return '/';
        }
        if ('b' === $escapedChar) {
            return "\x8";
        }
        if ('f' === $escapedChar) {
            return "\f";
        }
        if ('n' === $escapedChar) {
            return "\n";
        }
        if ('r' === $escapedChar) {
            return "\r";
        }
        if ('t' === $escapedChar) {
            return "\t";
        }

        throw new MalformedTjsonException('Invalid escaped character in UnicodeString');
    }
}
