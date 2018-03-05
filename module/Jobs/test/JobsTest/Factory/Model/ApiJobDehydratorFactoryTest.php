<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace JobsTest\Factory\Model;

use Jobs\Factory\Model\ApiJobDehydratorFactory;
use Zend\View\Helper\Url;

/**
 * Tests for ApplyUrl view helper factory
 *
 * @covers \Jobs\Factory\Model\ApiJobDehydratorFactory
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.Model
 */
class ApiJobDehydratorFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', new ApiJobDehydratorFactory());
    }

    /**
     * @testdox createService creates an ApplyUrl view helper and injects the required dependencies
     */
    public function testServiceCreation()
    {
        $target = new ApiJobDehydratorFactory();

        $urlHelper = new Url();

        $helpers = $this->getMockBuilder('\Zend\View\HelperPluginManager')
                        ->disableOriginalConstructor()
                        ->getMock();

        $helpers->expects($this->once())->method('get')
                ->with('url')
                ->willReturn($urlHelper);

        $serviceManagerMock = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $serviceManagerMock->expects($this->once())->method('get')
                           ->with('ViewHelperManager')
                           ->willReturn($helpers);


        $service = $target->__invoke($serviceManagerMock,'irrelevant');

        $this->assertInstanceOf('\Jobs\Model\ApiJobDehydrator', $service);
        $this->assertAttributeSame($urlHelper, 'url', $service);
    }
}
