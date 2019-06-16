<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:00
 */

namespace App\Components\Forms\SignForm;


use App\Components\Forms\FormFactory;

/**
 * Class SignFormFactory
 * @package App\Components\Forms\SignForm
 */
class SignFormFactory extends FormFactory
{
    /**
     * @return SignFormControl
     */
    public function create(): SignFormControl
    {
        return new SignFormControl($this->validationService);
    }
}