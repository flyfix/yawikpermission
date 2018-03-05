<?php
/**
 * YAWIK
 * Applications Module Bootstrap
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Applications;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Core\ModuleManager\ModuleConfigLoader;

/**
 * Bootstrap class of the applications module
 */
class Module implements ConsoleUsageProviderInterface, AutoloaderProviderInterface
{
    /**
     * Displays console options
     *
     * @param Console $console
     * @return array|null|string
     */
    public function getConsoleUsage(Console $console)
    {
        return array(
            'Manipulation of applications collection',
            'applications generatekeywords' => '(Re-)Generates keywords for all applications.',
            'applications calculate-rating' => '(Re-)Calculates average rating for all applications.',
            'applications cleanup'          => 'removes applications drafts.',
            'applications list'             => 'list view scripts.',
            'applications reset-files-permissions [--filter=]' => 'Resets (means: Set again) the permissions of attachments and contact images',
            array('--filter=JSON', "available keys:\n"
                                   . "- before    ISODate   only applications before the given date\n"
                                   . "- after     ISODate   only applications after the given date\n"
                                   . "- id        String    Mongo ID of the application\n"
                                   . "- isDraft   Boolean   "),
        );
    }
    
    /**
     * Loads module specific configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__ . '/config');
    }

    /**
     * Loads module specific autoloader configuration.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    __NAMESPACE__ . 'Test' => __DIR__ . '/test/' . __NAMESPACE__ .'Test',
                ),
            ),
        );
    }
}
