<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 11:55
 */

namespace App\Components\Forms\LogoForm;


use App\Components\Forms\IFormFactory;

/**
 * Interface ILogoIFormFactory
 * @package App\Components\Forms\LogoForm
 */
interface ILogoIFormFactory extends IFormFactory
{
    /**
     * @return LogoFormControl
     */
    public function create(): LogoFormControl;
}