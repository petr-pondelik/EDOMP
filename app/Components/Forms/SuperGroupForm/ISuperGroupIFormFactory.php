<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 18:43
 */

namespace App\Components\Forms\SuperGroupForm;

use App\Components\Forms\IFormFactory;

/**
 * Interface ISuperGroupIFormFactory
 * @package App\Components\Forms\SuperGroupForm
 */
interface ISuperGroupIFormFactory extends IFormFactory
{
    /**
     * @return SuperGroupFormControl
     */
    public function create(): SuperGroupFormControl;
}