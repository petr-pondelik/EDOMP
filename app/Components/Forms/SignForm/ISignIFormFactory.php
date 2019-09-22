<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:00
 */

namespace App\Components\Forms\SignForm;


use App\Components\Forms\IFormFactory;

/**
 * Interface ISignIFormFactory
 * @package App\Components\Forms\SignForm
 */
interface ISignIFormFactory extends IFormFactory
{
    /**
     * @param bool $admin
     * @return SignFormControl
     */
    public function create(bool $admin = false): SignFormControl;
}