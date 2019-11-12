<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 19:54
 */

namespace App\TeacherModule\Components\Forms\GroupForm;


use App\CoreModule\Components\Forms\IFormFactory;

/**
 * Interface IGroupFormFactory
 * @package App\TeacherModule\Components\Forms\GroupForm
 */
interface IGroupFormFactory extends IFormFactory
{
    /**
     * @return GroupFormControl
     */
    public function create(): GroupFormControl;
}