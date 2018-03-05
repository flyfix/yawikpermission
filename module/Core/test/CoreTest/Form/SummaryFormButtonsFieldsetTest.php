<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace CoreTest\Form;

use Core\Form\SummaryFormButtonsFieldset;

/**
* @covers \Core\Form\SummaryFormButtonsFieldset
*/
class SummaryFormButtonsFieldsetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SummaryFormButtonsFieldset
     */
    protected $target;

    public function setUp(){
        $this->target = new SummaryFormButtonsFieldset();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\SummaryFormButtonsFieldset', $this->target);
        $this->assertInstanceOf('Core\Form\ButtonsFieldset', $this->target);
    }

    public function testInit()
    {
        /* @todo
         */
    }

    public function testSetGetFormId()
    {
        $id=123;
        $this->target->setFormId($id);
        $this->assertSame($this->target->getFormId(), (string)$id);
    }

    public function testSetAttribute()
    {
        $this->target->setAttribute('id',123);
        $this->assertSame($this->target->getFormId(), "123");
    }

    public function testSetAttributeSomeAttribute()
    {
        $this->target->setAttribute('foo','bar');
        $this->assertSame($this->target->getFormId(), false);
    }
}