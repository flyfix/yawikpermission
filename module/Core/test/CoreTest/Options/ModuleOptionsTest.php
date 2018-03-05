<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Core\Options;

use Core\Options\ModuleOptions as Options;
use CoreTestUtils\TestCase\SetupTargetTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 *
 * @covers \Core\Options\ModuleOptions
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Options
 */
class ModuleOptionsTest extends \PHPUnit_Framework_TestCase
{

    use TestSetterGetterTrait, SetupTargetTrait;

    /**
     * @var Options
     */
    protected $target = [
        'class' => Options::class
    ];

    /**
     * @var Options $options
     */
    protected $options;

    public function propertiesProvider()
    {
        return [
            ['siteLogo', [
                'value' => 'some-logo.jpg',
                'default' => '/Core/images/logo.jpg'
            ]],
            ['siteName', [
                'value' => 'MyName',
                'default' => 'YAWIK'
            ]],
            ['defaultTaxRate', [
                'value' => '21',
                'default' => '19'
            ]],
            ['defaultLanguage', [
                'value' => 'fr',
                'default' => 'en'
            ]],
            ['defaultCurrencyCode', [
                'value' => 'EUR',
                'default' => 'USD'
            ]],
            ['operator', [
                'value' => [
                    'companyShortName' => 'My Company',
                    'companyFullName'  => 'My Co KG',
                    'companyTax'       => 'My VAT Number',
                    'postalCode'       => 'MY Zip',
                    'city'             => 'My Cits',
                    'country'          => 'My country',
                    'street'           => 'MY Rath Dínen 112',
                    'name'             => 'MY Gimli & Legolas',
                    'email'            => 'me@example.com',
                    'fax'              => '+49-0815',
                    'homepage'         => 'https://example.com'],
                'default' =>  [
                    'companyShortName' => 'Example Company Name',
                    'companyFullName'  => 'Example Company Name Ltd. & Co KG',
                    'companyTax'       => 'Your VAT Number',
                    'postalCode'       => '4711',
                    'city'             => 'Froschmoorstetten',
                    'country'          => 'Auenland',
                    'street'           => 'Rath Dínen 112',
                    'name'             => 'Gimli & Legolas',
                    'email'            => 'name@example.com',
                    'fax'              => '+49-0815-4711',
                    'homepage'         => 'http://example.com']
            ]],

            ['defaultCurrencyCode', [
                'value' => 'EUR',
                'default' => 'USD'
            ]],
        ];
    }


    /**
     * @since 0.20
     */
    public function testGetSiteNameThrowsExceptionIfNotSet()
    {
        $this->setExpectedException(
             '\Core\Options\Exception\MissingOptionException',
             'Missing value for option "siteName"'
        );

        $this->target->setSiteName('');
        $this->target->getSiteName();
    }

    /**
     * @covers Core\Options\ModuleOptions::getSupportedLanguages
     * @covers Core\Options\ModuleOptions::setSupportedLanguages
     */
    public function testSetGetSupportedLanguages()
    {
        $supportedLanguages = array(
            'de' => 'de_DE',
            'fr' => 'fr',
            'es' => 'es',
            'it' => 'it');

        $this->target->setSupportedLanguages($supportedLanguages);
        $this->assertEquals($supportedLanguages, $this->target->getSupportedLanguages());
    }

    /**
     * @covers Core\Options\ModuleOptions::isDetectLanguage
     * @covers Core\Options\ModuleOptions::setDetectLanguage
     */
    public function testSetGetDetectLanguage()
    {
        $this->target->setDetectLanguage(true);
        $this->assertEquals(true, $this->target->isDetectLanguage());
    }

    /**
     * @since 0.20
     */
    public function testAllowsSettingAndGettingSystemMessageEmail()
    {
        $this->assertSame($this->target, $this->target->setSystemMessageEmail('test@mail'), 'Fluent interface broken');
        $this->assertEquals('test@mail', $this->target->getSystemMessageEmail());
    }

    /**
     * @since 0.20
     */
    public function testThrowsExceptionIfSystemMessageEmailIsNotSet()
    {
        $this->setExpectedException('\Core\Options\Exception\MissingOptionException', 'Missing value for option "systemMessageEmail"');

        $this->target->getSystemMessageEmail();
    }
}
