<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CoreTestUtils\Constraint;

/**
 * Constraint to assert that a class uses specific traits.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.26
 * @since  0.29 matches() accepts instances of \ReflectionClass.
 */
class UsesTraits extends \PHPUnit_Framework_Constraint
{
    /**
     * The traits that must be used.
     *
     * @var string[]
     */
    private $expectedTraits = [];

    /**
     * Stores the result of each tested trait for internal use.
     *
     * @var array
     */
    private $result = [];

    /**
     * Creates an instance.
     *
     * @param string[] $expectedTraits
     */
    public function __construct($expectedTraits = [])
    {
        $this->expectedTraits = (array) $expectedTraits;
        parent::__construct();
    }

    public function count()
    {
        return count($this->expectedTraits);
    }

    /**
     * Tests if a class is using all required traits.
     *
     * Returns true if and only if all traits specified in {@link $expectedTraits} are used by
     * the tested object or class.
     *
     * @param string|object|\ReflectionClass $other FQCN or an object
     *
     * @return bool
     *
     * @since 0.29 accepts instances of \ReflectionClass.
     */
    protected function matches($other)
    {
        $traits  = $other instanceOf \ReflectionClass ? $other->getTraitNames() : class_uses($other);
        $success = true;

        foreach ($this->expectedTraits as $expectedTrait) {
            $check                        = in_array($expectedTrait, $traits);
            $this->result[$expectedTrait] = $check;
            $success                      = $success && $check;
        }

        return $success;
    }

    protected function failureDescription($other)
    {
        $name = $other instanceOf \ReflectionClass ? $other->getName() : (is_string($other) ? $other : get_class($other));
        return $name . ' ' . $this->toString();
    }

    protected function additionalFailureDescription($other)
    {
        $traits = '';

        foreach ($this->result as $trait => $valid) {
            $traits .= sprintf("\n %s %s", $valid ? '+' : '-', $trait);
        }

        return $traits;
    }

    public function toString()
    {
        return 'uses required traits';
    }
}