<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:49
 */

namespace App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Model\Functionality\BaseFunctionality;

/**
 * Class QuadraticEqTemplateFormFactory
 * @package App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm
 */
class QuadraticEqTemplateFormFactory extends ProblemTemplateFormFactory
{
    /**
     * @param BaseFunctionality $functionality
     * @param bool $edit
     * @return mixed
     */
    public function create(BaseFunctionality $functionality, bool $edit = false)
    {
        return new QuadraticEqTemplateFormControl(
            $this->validator, $functionality, $this->difficultyRepository, $this->problemTypeRepository,
            $this->subCategoryRepository, $this->problemConditionRepository, $this->mathService, $this->constHelper, $edit
        );
    }
}