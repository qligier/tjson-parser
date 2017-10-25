<?php
namespace Kentin\TJSON;

class Token
{
    /**
     * Type of the token
     *
     * @var string
     */
    private $token;

    /**
     * Litteral value of the token
     *
     * @var string
     */
    private $value = '';

    /**
     * @param array $token
     * @throws \Exception If $token has less than 3 elements
     */
    public function __construct(array $token)
    {
        if (count($token) < 3) {
            throw new \Exception('Token array must have at least 3 values');
        }
        $this->token = (string)$token[0];
        $this->value = (string)$token[2];
    }

    /**
     * Check if the token is of type $type
     *
     * @param string $type
     * @return bool
     */
    public function is(string $type): bool
    {
        return $this->token === $type;
    }

    /**
     * Return the value of the token
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
