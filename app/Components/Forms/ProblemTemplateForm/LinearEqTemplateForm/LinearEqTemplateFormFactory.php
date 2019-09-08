<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:20
 */

namespace App\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm;

use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Plugins\LinearEquationPlugin;
use App\Services\PluginContainer;
use App\Services\ProblemTemplateSession;
use App\Services\ProblemTemplateState;
use App\Services\Validator;


/**
 * Class LinearEqTemplFormFactory
 * @package App\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm
 */
class LinearEqTemplateFormFactory extends ProblemTemplateFormFactory
{
    /**
     * LinearEqTemplateFormFactory constructor.
     * @param Validator $validator
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param LinearEquationPlugin $problemTemplatePlugin
     * @param PluginContainer $pluginContainer
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param ProblemTemplateSession $problemTemplateSession
     */
    public function __construct
    (
        Validator $validator, DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository, ProblemConditionTypeRepository $problemConditionTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        LinearEquationPlugin $problemTemplatePlugin,
        PluginContainer $pluginContainer,
        StringsHelper $stringsHelper, ConstHelper $constHelper,
        ProblemTemplateSession $problemTemplateSession
    )
    {
        parent::__construct
        (
            $validator, $difficultyRepository, $problemTypeRepository, $subCategoryRepository,
            $problemConditionTypeRepository, $problemConditionRepository,
            $pluginContainer,
            $stringsHelper, $constHelper,
            $problemTemplateSession
        );
        $this->problemTemplatePlugin = $problemTemplatePlugin;
    }

    /**
     * @param BaseFunctionality $functionality
     * @param bool $edit
     * @return LinearEqTemplateFormControl|mixed
     */
    public function create(BaseFunctionality $functionality, bool $edit = false)
    {
        return new LinearEqTemplateFormControl(
            $this->validator, $functionality, $this->difficultyRepository, $this->problemTypeRepository,
            $this->subCategoryRepository, $this->problemConditionTypeRepository, $this->problemConditionRepository,
            $this->problemTemplatePlugin,
            $this->pluginContainer,
            $this->stringsHelper, $this->constHelper,
            $this->problemTemplateSession,
            $edit
        );
    }
}