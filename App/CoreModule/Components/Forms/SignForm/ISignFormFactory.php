<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:00
 */

namespace App\CoreModule\Components\Forms\SignForm;


use App\CoreModule\Components\Forms\IFormFactory;

/**
 * Interface ISignFormFactory
 * @package App\CoreModule\Components\Forms\SignForm
 */
interface ISignFormFactory extends IFormFactory
{
    /**
     * @param bool $admin
     * @return SignFormControl
     */
    public function create(bool $admin = false): SignFormControl;
}