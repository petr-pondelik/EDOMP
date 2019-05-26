<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 18:43
 */

namespace App\Components\Forms\SuperGroupForm;

use App\Components\Forms\FormFactory;
use App\Model\Functionality\SuperGroupFunctionality;
use App\Services\ValidationService;

/**
 * Class SuperGroupFormFactory
 * @package App\Components\Forms\SuperGroupForm
 */
class SuperGroupFormFactory extends FormFactory
{
    /**
     * SuperGroupFormFactory constructor.
     * @param ValidationService $validationService
     * @param SuperGroupFunctionality $superGroupFunctionality
     */
    public function __construct(ValidationService $validationService, SuperGroupFunctionality $superGroupFunctionality)
    {
        parent::__construct($validationService);
        $this->functionality = $superGroupFunctionality;
    }

    /**
     * @param bool $edit
     * @return SuperGroupFormControl
     */
    public function create(bool $edit = false): SuperGroupFormControl
    {
        return new SuperGroupFormControl($this->validationService, $this->functionality, $edit);
    }
}