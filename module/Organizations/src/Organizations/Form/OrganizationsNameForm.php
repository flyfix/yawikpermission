<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Organizations\Form;

use Core\Form\SummaryForm;

class OrganizationsNameForm extends SummaryForm
{
    protected $baseFieldset = 'Organizations/OrganizationsNameFieldset';

    protected $displayMode = self::DISPLAY_SUMMARY;
}
