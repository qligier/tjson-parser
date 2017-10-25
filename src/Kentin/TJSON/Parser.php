<?php
namespace Kentin\TJSON;

use Phlexy\LexingException;
use Exception;

class Parser
{
    /**
     * List of tokens
     *
     * @var TokenList
     */
    private $tokenList;

    /**
     * Version of TJSON-parser
     *
     * @var string
     */
    const VERSION = '0.1.0';

    /**
     *
     */
    public function __construct()
    {
        $this->tokenList = new TokenList([]);
    }

    /**
     * Parse a TJSON string
     *
     * @param string $tjsonString
     *
     * @return array<string, string|\GMP|bool|\DateTime|float|array>
     */
    public function parse(string $tjsonString): array
    {
        $lexer = (new LexerFactory)->createLexer();

        try {
            $tokensArray = $lexer->lex($tjsonString);
        } catch (LexingException $e) {
            throw new MalformedTjsonException('Lexer error: '.$e->getMessage());
        }

        // Filter whitespace tokens
        $tokensArray = array_filter(
            $tokensArray,
            function (array $tokenArray): bool {
                return $tokenArray[0] !== Tokens::T_JSON_WHITESPACE;
            }
        );

        $tokens = array_map(
            function (array $tokenArray): Token {
                return new Token($tokenArray);
            },
            $tokensArray
        );
        $this->tokenList = new TokenList($tokens);

        $tjson = $this->consumeObject();

        if ($this->tokenList->valid()) {
            throw new MalformedTjsonException('Unexpected content after root object');
        }

        return $tjson;
    }

    /**
     * @return array<string, string|\GMP|bool|\DateTime|float|array>
     * @throws MalformedTjsonException
     */
    private function consumeObject(): array
    {
        $object = [];

        if (!$this->tokenList->current()->is(Tokens::T_JSON_LEFT_CURLY_BRACKET)) {
            throw new MalformedTjsonException('Objects must start with a curly bracket');
        }
        $this->tokenList->next();

        $afterComma = false;
        // Loop on object members
        while (!$this->tokenList->current()->is(Tokens::T_JSON_RIGHT_CURLY_BRACKET)) {
            $afterComma = false;

            // Expecting the member name
            $memberName = $this->consumeString();

            // Extract data type
            if (false === strpos($memberName, ':')) {
                throw new MalformedTjsonException('Missing type in object member name');
            }
            list(
                $memberName,
                $memberType
            ) = preg_split('~:(?=[^:]+$)~', $memberName);
            if (empty($memberName)) {
                throw new MalformedTjsonException('Member name can not be empty in objects');
            }
            $typeConstraint = new TypeConstraint($memberType);

            // Check name separator
            if (!$this->tokenList->current()->is(Tokens::T_JSON_COLON)) {
                throw new MalformedTjsonException('Member name separator not found in object');
            }
            $this->tokenList->next();

            // Extract the value
            $memberValue = $this->consumeByConstraint($typeConstraint);

            // Add member to object
            if (array_key_exists($memberName, $object)) {
                throw new MalformedTjsonException('Non-unique value in Object');
            }
            $object[$memberName] = $memberValue;

            // Check for comma
            if ($this->tokenList->current()->is(Tokens::T_JSON_COMMA)) {
                $afterComma = true;
                $this->tokenList->next();
            }
        }
        $this->tokenList->next();

        if ($afterComma) {
            throw new MalformedTjsonException('Objects can not have a trailing comma');
        }

        return $object;
    }

    /**
     * @param TypeConstraint|null $constraint
     *
     * @return array<string|\GMP|bool|\DateTime|float|array>
     * @throws MalformedTjsonException
     */
    private function consumeArray(TypeConstraint $constraint = null): array
    {
        $array = [];

        if (!$this->tokenList->current()->is(Tokens::T_JSON_LEFT_SQUARE_BRACKET)) {
            throw new MalformedTjsonException('Arrays must start with a square bracket');
        }
        $this->tokenList->next();

        $afterComma = false;
        // Loop on array members
        while (!$this->tokenList->current()->is(Tokens::T_JSON_RIGHT_SQUARE_BRACKET)) {
            $afterComma = false;

            // Extract the value
            if (null === $constraint) {
                throw new MalformedTjsonException('Non-empty arrays must have an inner type definition');
            }
            $value = $this->consumeByConstraint($constraint);

            // Add member to array
            $array[] = $value;

            // Check for comma
            if ($this->tokenList->current()->is(Tokens::T_JSON_COMMA)) {
                $afterComma = true;
                $this->tokenList->next();
            }
        }
        $this->tokenList->next();

        if ($afterComma) {
            throw new MalformedTjsonException('Arrays can not have a trailing comma');
        }

        return $array;
    }

    /**
     * @param TypeConstraint|null $constraint
     *
     * @return array<string|\GMP|bool|\DateTime|float|array>
     * @throws MalformedTjsonException
     */
    private function consumeSet(TypeConstraint $constraint = null): array
    {
        $set = [];

        if (!$this->tokenList->current()->is(Tokens::T_JSON_LEFT_SQUARE_BRACKET)) {
            throw new MalformedTjsonException('Sets must start with a square bracket');
        }
        $this->tokenList->next();

        $afterComma = false;
        // Loop on set members
        while (!$this->tokenList->current()->is(Tokens::T_JSON_RIGHT_SQUARE_BRACKET)) {
            $afterComma = false;

            // Extract the value
            if (null === $constraint) {
                throw new MalformedTjsonException('Non-empty sets must have an inner type definition');
            }
            $value = $this->consumeByConstraint($constraint);

            // Add member to set
            if (in_array($value, $set)) {
                throw new MalformedTjsonException('Non-unique value in Set');
            }
            $set[] = $value;

            // Check for comma
            if ($this->tokenList->current()->is(Tokens::T_JSON_COMMA)) {
                $afterComma = true;
                $this->tokenList->next();
            }
        }
        $this->tokenList->next();

        if ($afterComma) {
            throw new MalformedTjsonException('Sets can not have a trailing comma');
        }

        return $set;
    }

    /**
     * @return \GMP
     */
    private function consumeSignedInteger(): \GMP
    {
        $signedInteger = new Types\SignedInteger;
        return $signedInteger->transform($this->consumeString());
    }

    /**
     * @return \GMP
     */
    private function consumeUnsignedInteger(): \GMP
    {
        $unsignedInteger = new Types\UnsignedInteger;
        return $unsignedInteger->transform($this->consumeString());
    }

    /**
     * @return float
     */
    private function consumeFloatingPoint(): float
    {
        if (!$this->tokenList->current()->is(Tokens::T_JSON_NUMBER)) {
            throw new MalformedTjsonException('Expected FloatingPoint');
        }

        $floatingPoint = new Types\FloatingPoint;
        $value = $floatingPoint->transform($this->tokenList->current()->getValue());
        $this->tokenList->next();
        return $value;
    }

    /**
     * @return string
     */
    private function consumeString(): string
    {
        if (!$this->tokenList->current()->is(Tokens::T_JSON_STRING)) {
            throw new MalformedTjsonException('Expected UnicodeString');
        }

        $unicodeString = new Types\UnicodeString;
        $value = $unicodeString->transform($this->tokenList->current()->getValue());
        $this->tokenList->next();
        return $value;
    }

    /**
     * @return \DateTime
     */
    private function consumeTimestamp(): \DateTime
    {
        $timestamp = new Types\Timestamp;
        return $timestamp->transform($this->consumeString());
    }

    /**
     * @return bool
     */
    private function consumeBoolean(): bool
    {
        if (
            !$this->tokenList->current()->is(Tokens::T_JSON_TRUE)
            && !$this->tokenList->current()->is(Tokens::T_JSON_FALSE)
        ) {
            throw new MalformedTjsonException('Expected Boolean');
        }

        $boolean = new Types\Boolean;
        $value = $boolean->transform($this->tokenList->current()->getValue());
        $this->tokenList->next();
        return $value;
    }

    /**
     * @return string
     */
    private function consumeBase16(): string
    {
        $base16 = new Types\Base16;
        return $base16->transform($this->consumeString());
    }

    /**
     * @return string
     */
    private function consumeBase32(): string
    {
        $base32 = new Types\Base32;
        return $base32->transform($this->consumeString());
    }

    /**
     * @return string
     */
    private function consumeBase64Url(): string
    {
        $base64Url = new Types\Base64Url;
        return $base64Url->transform($this->consumeString());
    }

    /**
     * @param TypeConstraint $constraint
     *
     * @return string|\GMP|bool|\DateTime|float|array
     * @throws Exception If the type constraint is invalid
     */
    private function consumeByConstraint(TypeConstraint $constraint)
    {
        if ('s' === $constraint->getType()) {
            return $this->consumeString();
        }
        if ('u' === $constraint->getType()) {
            return $this->consumeUnsignedInteger();
        }
        if ('i' === $constraint->getType()) {
            return $this->consumeSignedInteger();
        }
        if ('t' === $constraint->getType()) {
            return $this->consumeTimestamp();
        }
        if ('f' === $constraint->getType()) {
            return $this->consumeFloatingPoint();
        }
        if ('b' === $constraint->getType()) {
            return $this->consumeBoolean();
        }
        if ('d16' === $constraint->getType()) {
            return $this->consumeBase16();
        }
        if ('d32' === $constraint->getType()) {
            return $this->consumeBase32();
        }
        if ('d64' === $constraint->getType()) {
            return $this->consumeBase64Url();
        }
        if ('A' === $constraint->getType()) {
            return $this->consumeArray($constraint->getInnerConstraint());
        }
        if ('S' === $constraint->getType()) {
            return $this->consumeSet($constraint->getInnerConstraint());
        }
        if ('O' === $constraint->getType()) {
            return $this->consumeObject();
        }

        throw new Exception('Unknown type to consume');
    }
}
