<?php
namespace Kentin\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;

class Base64Url implements ScalarType
{
    /**
     * Regex that match the valid formats of base64url-encoded strings
     *
     * @var string
     */
    const FORMAT_REGEX = '~[a-zA-Z0-9-_]*$~A';

    /**
     * @param string $bytes
     *
     * @return string
     * @throws MalformedTjsonException If $bytes is an invalid base64Url string
     */
    public function transform(string $bytes): string
    {
        if (!\preg_match(self::FORMAT_REGEX, $bytes)) {
            throw new MalformedTjsonException('Invalid Base64Url format');
        }

        /**
         * @var string|false
         */
        $decodedString = base64_decode($this->translateToBase64($bytes), true);
        if (false === $decodedString) {
            throw new MalformedTjsonException('Invalid Base64Url format');
        }

        return $decodedString;
    }

    /**
     * Translate a base64url-encoded string to base64
     *
     * @param string $base64url
     *
     * @return string
     */
    public function translateToBase64(string $base64url): string
    {
        return str_replace(['-', '_'], ['+', '/'], $base64url);
    }
}
