<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:00
 */

namespace App\Components\Forms\SignForm;


use App\Components\Forms\BaseFormFactory;

/**
 * Class SignFormFactory
 * @package App\Components\Forms\SignForm
 */
class SignFormFactory extends BaseFormFactory
{
    /**
     * @return SignFormControl
     */
    public function create(): SignFormControl
    {
        return new SignFormControl($this->validationService);
    }
}