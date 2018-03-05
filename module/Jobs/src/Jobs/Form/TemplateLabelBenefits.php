<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Core\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Jobs\Form\Hydrator\TemplateLabelHydrator;

/**
 * Defines the label of the field "benefits" of a job opening template
 *
 * @package Jobs\Form
 */
class TemplateLabelBenefits extends Form implements InputFilterProviderInterface
{

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new TemplateLabelHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function init()
    {
        $this->setName('jobs-form-label-benefits');
        $this->setAttributes(
            array(
            'id' => 'jobs-form-label-benefits',
            'data-handle-by' => 'yk-form'
            )
        );

        $this->add(
            [
                'type' => 'Text',
                'name' => 'description-label-benefits',
                'options' => [
                    'use_as_base_fieldset' => true,
                ]
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return array(
            'description-label-benefits' => array(
                'filters' => array(
                    array(
                        'name' => 'Core/XssFilter'
                    )
                )
            )
       );
    }
}
