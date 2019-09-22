<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 19:54
 */

namespace App\Components\Forms\GroupForm;


use App\Components\Forms\IFormFactory;

/**
 * Interface IGroupIFormFactory
 * @package App\Components\Forms\GroupForm
 */
interface IGroupIFormFactory extends IFormFactory
{
    /**
     * @return GroupFormControl
     */
    public function create(): GroupFormControl;
}