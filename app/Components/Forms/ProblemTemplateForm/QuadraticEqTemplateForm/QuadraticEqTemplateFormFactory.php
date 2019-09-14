<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:49
 */

namespace App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use App\Model\Persistent\Functionality\ProblemTemplate\QuadraticEquationTemplateFunctionality;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Plugins\QuadraticEquationPlugin;
use App\Services\PluginContainer;
use App\Services\ProblemTemplateSession;
use App\Services\Validator;

/**
 * Class QuadraticEqTemplateFormFactory
 * @package App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm
 */
class QuadraticEqTemplateFormFactory extends ProblemTemplateFormFactory
{
    /**
     * QuadraticEqTemplateFormFactory constructor.
     * @param Validator $validator
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param QuadraticEquationPlugin $quadraticEquationPlugin
     * @param PluginContainer $pluginContainer
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param ProblemTemplateSession $problemTemplateSession
     * @param QuadraticEquationTemplateFunctionality $quadraticEquationTemplateFunctionality
     */
    public function __construct
    (
        Validator $validator,
        DifficultyRepository $difficultyRepository,
        ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        QuadraticEquationPlugin $quadraticEquationPlugin,
        PluginContainer $pluginContainer,
        StringsHelper $stringsHelper,
        ConstHelper $constHelper,
        ProblemTemplateSession $problemTemplateSession,
        QuadraticEquationTemplateFunctionality $quadraticEquationTemplateFunctionality
    )
    {
        parent::__construct(
            $validator, $difficultyRepository, $problemTypeRepository, $subCategoryRepository,
            $problemConditionTypeRepository, $problemConditionRepository,
            $pluginContainer,
            $stringsHelper, $constHelper,
            $problemTemplateSession
        );
        $this->functionality = $quadraticEquationTemplateFunctionality;
        $this->problemTemplatePlugin = $quadraticEquationPlugin;
    }

    /**
     * @param bool $edit
     * @return mixed
     */
    public function create(bool $edit = false)
    {
        return new QuadraticEqTemplateFormControl(
            $this->validator, $this->functionality, $this->difficultyRepository, $this->problemTypeRepository,
            $this->subCategoryRepository, $this->problemConditionTypeRepository, $this->problemConditionRepository,
            $this->problemTemplatePlugin,
            $this->pluginContainer,
            $this->stringsHelper, $this->constHelper,
            $this->problemTemplateSession,
            $edit
        );
    }
}