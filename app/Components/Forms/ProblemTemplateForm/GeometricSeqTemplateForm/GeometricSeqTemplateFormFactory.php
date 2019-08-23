<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:56
 */

namespace ProblemTemplateForm\GeometricSeqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Model\Persistent\Functionality\BaseFunctionality;

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
            $this->validator, $functionality, $this->difficultyRepository, $this->problemTypeRepository,
            $this->subCategoryRepository, $this->problemConditionTypeRepository, $this->problemConditionRepository,
            $this->mathService, $this->constHelper, $edit
        );
    }
}