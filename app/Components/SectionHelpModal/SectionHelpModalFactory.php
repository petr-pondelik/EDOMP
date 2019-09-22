<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 16.6.19
 * Time: 23:21
 */

namespace App\Components\SectionHelpModal;


/**
 * Interface SectionHelpModalFactory
 * @package App\Components\ProblemTemplateHelp
 */
interface SectionHelpModalFactory
{
    /**
     * @return SectionHelpModalControl
     */
    public function create(): SectionHelpModalControl;
}