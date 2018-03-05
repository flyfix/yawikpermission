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
 * Constraint to assert the extending or implementing of specific classes and interfaces.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.26
 * @since  0.29 matches() allow passing in a \ReflectionClass object instance.
 */
class ExtendsOrImplements extends \PHPUnit_Framework_Constraint
{
    /**
     * The FQCN of the classes and interfaces which the tested object
     * must extend or implement.
     *
     * @var string[]
     */
    private $parentsAndInterfaces = [];

    /**
     * Stores the result of each tested class|interface for internal use.
     *
     * @var array
     */
    private $result = [];

    /**
     * Creates a new instance.
     *
     * @param string[] $parentsAndInterfaces FQCNs of classes or interfaces.
     */
    public function __construct($parentsAndInterfaces = [])
    {
        $this->parentsAndInterfaces = (array) $parentsAndInterfaces;
        parent::__construct();
    }

    public function count()
    {
        return count($this->parentsAndInterfaces);
    }

    /**
     * Tests if an object extends or implements the required classes or interfaces.
     *
     * Returns true, if and only if the object extends or implements ALL the classes and interfaces
     * provided with {@link $parentsAndInterfaces}
     *
     * @param object|\ReflectionClass $other
     *
     * @return bool
     *
     * @since 0.29 $other can be an instance of \ReflectionClass.
     */
    protected function matches($other)
    {
        $this->result = [];
        $success      = true;
        $isReflection = $other instanceOf \ReflectionClass;

        foreach ($this->parentsAndInterfaces as $fqcn) {
            $check               = $isReflection ? $other->isSubclassOf($fqcn) : $other instanceOf $fqcn;
            $this->result[$fqcn] = $check;
            $success             = $success && $check;
        }

        return $success;
    }

    protected function failureDescription($other)
    {
        return ($other instanceOf \ReflectionClass ? $other->getName() : get_class($other)) . ' ' . $this->toString();
    }

    protected function additionalFailureDescription($other)
    {
        $info = '';

        foreach ($this->result as $fqcn => $valid) {
            $info .= sprintf("\n %s %s", $valid ? '+' : '-', $fqcn);
        }

        return $info;
    }

    public function toString()
    {
        return 'extends or implements required classes and interfaces';
    }


}