<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.7.19
 * Time: 10:48
 */

namespace App\Components\SectionHelpModal;

/**
 * Interface ISectionHelpModalFactory
 * @package App\Components\SectionHelpModal
 */
interface ISectionHelpModalFactory
{
    /**
     * @return SectionHelpModalControl
     */
    public function create(): SectionHelpModalControl;
}