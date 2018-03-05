<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace AuthTest\Entity;

use Auth\Entity\AuthSession;

/**
 * Tests for User
 *
 * @covers \Auth\Entity\User
 * @coversDefaultClass \Auth\Entity\User
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  User
 * @group  User.Entity
 */
class AuthSessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
     *
     * @var AuthSession
     */
    private $target;

    public function setup()
    {
        $this->target = new AuthSession();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity
     * @coversNothing
     */
    public function testExtendsAbstractEntity()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
    }

    /**
     * @testdox Allows to set the name of the session
     * @covers Auth\Entity\AuthSession::getName
     * @covers Auth\Entity\AuthSession::setName
     */
    public function testSetGetName()
    {
        $input = 'YawikSession';
        $this->target->setName($input);
        $this->assertEquals($input, $this->target->getName());
    }

    public function provideSessionTestData()
    {
        $data = ["a"=>"b","c"=>[1,2,3,4]];
        $serialized=serialize($data);

        return [
            [$data, $data],
            [$serialized, $data]
        ];
    }


    /**
     * @testdox Allows to set the values of the session
     * @covers Auth\Entity\AuthSession::getSession
     * @covers Auth\Entity\AuthSession::setSession
     * @dataProvider provideSessionTestData
     */
    public function testSetGetSession($session, $expectedSession)
    {
        $input = $session;

        $this->target->setSession($input);
        $this->assertEquals($input, $this->target->getSession());
    }


    public function provideModificationDateTestData()
    {
        $date = "2015-01-12 12:11:06";
        return [
            [null,                 new \DateTime()],
            [new \DateTime($date), new \DateTime($date)],
            [$date,                new \DateTime($date)],
        ];
    }
    /**
     * @testdox Allows to set the role name of a user
     * @covers Auth\Entity\AuthSession::setModificationDate
     * @covers Auth\Entity\AuthSession::getModificationDate
     * @dataProvider provideModificationDateTestData
     */
    public function testSetGetModificationDate($date, $expectedDate)
    {
        $this->target->setModificationDate($date);

        if (null == $date) {
            $this->assertInstanceOf("\DateTime", $this->target->getModificationDate());
        } else {
            $this->assertEquals($expectedDate, $this->target->getModificationDate());
        }
    }
}
