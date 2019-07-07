<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:58
 */

namespace App\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm;

use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Model\Functionality\BaseFunctionality;

/**
 * Class ArithmeticSeqTemplateFormFactory
 * @package App\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm
 */
class ArithmeticSeqTemplateFormFactory extends ProblemTemplateFormFactory
{
    /**
     * @param BaseFunctionality $functionality
     * @param bool $edit
     * @return mixed
     */
    public function create(BaseFunctionality $functionality, bool $edit = false)
    {
        return new ArithmeticSeqTemplateFormControl(
            $this->validationService, $functionality, $this->difficultyRepository, $this->problemTypeRepository,
            $this->subCategoryRepository, $this->problemConditionRepository, $this->mathService, $this->constHelper, $edit
        );
    }
}