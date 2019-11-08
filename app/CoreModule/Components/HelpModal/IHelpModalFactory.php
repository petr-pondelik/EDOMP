<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.7.19
 * Time: 10:48
 */

namespace App\CoreModule\Components\HelpModal;

/**
 * Interface IHelpModalFactory
 * @package App\CoreModule\Components\SectionHelpModal
 */
interface IHelpModalFactory
{
    /**
     * @return HelpModalControl
     */
    public function create(): HelpModalControl;
}