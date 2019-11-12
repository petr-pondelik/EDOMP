<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 11:55
 */

namespace App\TeacherModule\Components\Forms\LogoForm;


use App\CoreModule\Components\Forms\IFormFactory;

/**
 * Interface ILogoIFormFactory
 * @package App\TeacherModule\Components\Forms\LogoForm
 */
interface ILogoFormFactory extends IFormFactory
{
    /**
     * @return LogoFormControl
     */
    public function create(): LogoFormControl;
}