<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 18:43
 */

namespace App\Components\Forms\SuperGroupForm;

use App\Components\Forms\BaseFormFactory;
use App\Model\Functionality\SuperGroupFunctionality;
use App\Service\ValidationService;

/**
 * Class SuperGroupFormFactory
 * @package App\Components\Forms\SuperGroupForm
 */
class SuperGroupFormFactory extends BaseFormFactory
{
    /**
     * @var SuperGroupFunctionality
     */
    protected $superGroupFunctionality;

    /**
     * SuperGroupFormFactory constructor.
     * @param ValidationService $validationService
     * @param SuperGroupFunctionality $superGroupFunctionality
     */
    public function __construct(ValidationService $validationService, SuperGroupFunctionality $superGroupFunctionality)
    {
        parent::__construct($validationService);
        $this->superGroupFunctionality = $superGroupFunctionality;
    }

    /**
     * @param bool $edit
     * @return SuperGroupFormControl
     */
    public function create(bool $edit = false): SuperGroupFormControl
    {
        return new SuperGroupFormControl($this->validationService, $this->superGroupFunctionality, $edit);
    }
}