<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 16.6.19
 * Time: 23:21
 */

namespace App\Components\ProblemTemplateHelp;

use App\Components\IControlFactory;

/**
 * Class ProblemTemplateHelpFactory
 * @package App\Components\ProblemTemplateHelp
 */
class SectionHelpModalFactory implements IControlFactory
{
    /**
     * @return SectionHelpModalControl
     */
    public function create(): SectionHelpModalControl
    {
        return new SectionHelpModalControl();
    }
}