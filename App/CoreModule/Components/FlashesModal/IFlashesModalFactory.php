<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 19.12.19
 * Time: 12:39
 */

namespace App\CoreModule\Components\FlashesModal;

/**
 * Interface IFlashesModalFactory
 * @package App\CoreModule\Components\FlashesModal
 */
interface IFlashesModalFactory
{
    /**
     * @return FlashesModalControl
     */
    public function create(): FlashesModalControl;
}