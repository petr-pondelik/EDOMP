<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 18:43
 */

namespace App\Components\Forms\SuperGroupForm;

use App\Components\Forms\FormFactory;
use App\Model\Persistent\Functionality\SuperGroupFunctionality;
use App\Services\Validator;

/**
 * Class SuperGroupFormFactory
 * @package App\Components\Forms\SuperGroupForm
 */
class SuperGroupFormFactory extends FormFactory
{
    /**
     * SuperGroupFormFactory constructor.
     * @param Validator $validator
     * @param SuperGroupFunctionality $superGroupFunctionality
     */
    public function __construct(Validator $validator, SuperGroupFunctionality $superGroupFunctionality)
    {
        parent::__construct($validator);
        $this->functionality = $superGroupFunctionality;
    }

    /**
     * @param bool $edit
     * @return SuperGroupFormControl
     */
    public function create(bool $edit = false): SuperGroupFormControl
    {
        return new SuperGroupFormControl($this->validator, $this->functionality, $edit);
    }
}