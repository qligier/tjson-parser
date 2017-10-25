<?php
namespace Kentin\TJSON\Types;

use Kentin\TJSON\MalformedTjsonException;
use DateTime;

class Timestamp implements ScalarType
{
    /**
     * Regex that match the valid formats of signed integers
     *
     * @var string
     */
    const FORMAT_REGEX = '~\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$~A';

    /**
     * @param string $bytes
     *
     * @return DateTime
     * @throws MalformedTjsonException
     */
    public function transform(string $bytes): DateTime
    {
        if (!\preg_match(self::FORMAT_REGEX, $bytes)) {
            throw new MalformedTjsonException('Invalid Timestamp format');
        }

        $timestamp = DateTime::createFromFormat('Y-m-d\TH:i:sP', $bytes);

        if (false === $timestamp) {
            throw new MalformedTjsonException('Timestamp format is invalid');
        }

        return $timestamp;
    }
}
