<?php
namespace Kentin\TJSON;

class TokenList implements \Iterator, \ArrayAccess
{
    /**
     * List of tokens
     *
     * @var Token[]
     */
    private $tokens = [];

    /**
     * Length of the token array
     *
     * @var int
     */
    private $length = 0;

    /**
     * Current position of the iterator
     *
     * @var int
     */
    private $position = 0;

    /**
     * @param Token[] $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = array_values($tokens);
        $this->length = count($tokens);
        $this->rewind();
    }

    /**
     * Return the current token
     *
     * @return Token
     */
    public function current(): Token
    {
        if (!$this->valid()) {
            throw new MalformedTjsonException('Unexpected end of TJSON');
        }
        return $this->tokens[$this->position];
    }

    /**
     * Return the key of the current token
     *
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Move forward to next token
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Rewind the Iterator to the first token
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->tokens[$this->position]);
    }

    /**
     * Return whether an offset exists
     */
    public function offsetExists($offset)
    {
        return isset($this->tokens[$offset]);
    }

    /**
     * Returns the token at specified offset
     *
     * @param int $offset
     * @return Token|null
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->tokens[$offset];
        }
        return null;
    }

    /**
     * Assign a token to the specified offset
     *
     * @param int $offset
     * @param Token $token
     */
    public function offsetSet($offset, $token)
    {
        $this->tokens[$offset] = $token;
    }

    /**
     * Unset an offset
     *
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->tokens[$offset]);
        $this->tokens = array_values($this->tokens);
    }

    /**
     * Return the token list
     *
     * @return Token[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }
}
