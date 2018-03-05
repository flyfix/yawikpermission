<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Entity\Hydrator;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityHydratorFactory implements FactoryInterface
{
    protected $hydrator;
	
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$this->hydrator = $this->getEntityHydrator();
		$this->prepareHydrator();
		return $this->hydrator;
	}
	
	
	/**
     * Create the Json Entity Hydrator
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return JsonEntityHydrator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
		return $this($serviceLocator,EntityHydrator::class);
    }

    protected function prepareHydrator()
    {
    }

    protected function getEntityHydrator()
    {
        return new EntityHydrator();
    }
}
