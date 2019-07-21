<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:20
 */

namespace App\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm;

use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Model\Functionality\BaseFunctionality;


/**
 * Class LinearEqTemplFormFactory
 * @package App\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm
 */
class LinearEqTemplateFormFactory extends ProblemTemplateFormFactory
{
    /**
     * @param BaseFunctionality $functionality
     * @param bool $edit
     * @return LinearEqTemplateFormControl|mixed
     */
    public function create(BaseFunctionality $functionality, bool $edit = false)
    {
        return new LinearEqTemplateFormControl(
            $this->validator, $functionality, $this->difficultyRepository, $this->problemTypeRepository,
            $this->subCategoryRepository, $this->problemConditionRepository, $this->mathService, $this->constHelper, $edit
        );
    }
}