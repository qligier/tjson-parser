<?php
namespace Kentin\TJSON;

class TypeConstraint
{
    /**
     * Type fixed by the constraint
     *
     * @var string
     */
    private $typeConstraint = '';

    /**
     * Constraint of the inner type
     *
     * @var TypeConstraint|null
     */
    private $innerConstraint = null;

    /**
     * Regex that validates a valid TJSON type
     *
     * @const TYPE_REGEX
     */
    const TYPE_REGEX = '~^(?<type>b|d|d64|d32|d16|f|i|u|s|t|A|O|S)(?:<(?<innerType>.*)>)?$~';

    /**
     * @param string $typeConstraint
     */
    public function __construct(string $typeConstraint)
    {
        $groups = [];
        if (
            false === preg_match(self::TYPE_REGEX, $typeConstraint, $groups)
            || empty($groups['type'])
        ) {
            throw new MalformedTjsonException('Unknown datatype');
        }

        // Array and Set must have an inner type definition
        if ('A' === $groups['type'] || 'S' === $groups['type']) {
            if (!isset($groups['innerType'])) {
                throw new MalformedTjsonException('Arrays and Sets must have an inner type definition');
            }
            $this->typeConstraint = $groups['type'];
            if (!empty($groups['innerType'])) {
                $this->innerConstraint = new TypeConstraint($groups['innerType']);
            }
        }
        // Others don't
        else {
            if (isset($groups['innerType'])) {
                throw new MalformedTjsonException('Scalar types and Objects can not have an inner type definition');
            }
            $this->typeConstraint = $groups['type'];
        }

        // Use d64 instead of d
        if ('d' === $this->typeConstraint) {
            $this->typeConstraint = 'd64';
        }
    }

    /**
     * Get the type specified by the constraint
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->typeConstraint;
    }

    /**
     * Return the inner TypeConstraint
     *
     * @return TypeConstraint|null
     */
    public function getInnerConstraint()
    {
        return $this->innerConstraint;
    }
}
