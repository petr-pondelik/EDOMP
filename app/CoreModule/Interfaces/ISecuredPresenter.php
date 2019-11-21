<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.11.19
 * Time: 16:59
 */

namespace App\CoreModule\Interfaces;

/**
 * Interface ISecuredPresenter
 * @package App\CoreModule\Interfaces
 */
interface ISecuredPresenter
{
    public function secure(): void;
}