<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:56
 */

namespace ProblemTemplateForm\GeometricSeqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Model\Functionality\BaseFunctionality;

/**
 * Class GeometricSeqTemplateFormFactory
 * @package ProblemTemplateForm\GeometricSeqTemplateForm
 */
class GeometricSeqTemplateFormFactory extends ProblemTemplateFormFactory
{

    /**
     * @param BaseFunctionality $functionality
     * @param bool $edit
     * @return mixed
     */
    public function create(BaseFunctionality $functionality, bool $edit = false)
    {
        return new GeometricSeqTemplateFormControl(
            $this->validationService, $functionality, $this->difficultyRepository, $this->problemTypeRepository,
            $this->subCategoryRepository, $this->problemConditionRepository, $this->mathService, $this->constHelper, $edit
        );
    }
}