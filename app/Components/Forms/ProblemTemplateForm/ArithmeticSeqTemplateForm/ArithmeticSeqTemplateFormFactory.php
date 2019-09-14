<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:58
 */

namespace App\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm;

use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use App\Model\Persistent\Functionality\ProblemTemplate\ArithmeticSequenceTemplateFunctionality;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Plugins\ArithmeticSequencePlugin;
use App\Services\PluginContainer;
use App\Services\ProblemTemplateSession;
use App\Services\Validator;

/**
 * Class ArithmeticSeqTemplateFormFactory
 * @package App\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm
 */
class ArithmeticSeqTemplateFormFactory extends ProblemTemplateFormFactory
{
    /**
     * ArithmeticSeqTemplateFormFactory constructor.
     * @param Validator $validator
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param ArithmeticSequencePlugin $problemTemplatePlugin
     * @param PluginContainer $pluginContainer
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param ProblemTemplateSession $problemTemplateSession
     * @param ArithmeticSequenceTemplateFunctionality $arithmeticSequenceTemplateFunctionality
     */
    public function __construct
    (
        Validator $validator, DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository, ProblemConditionTypeRepository $problemConditionTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        ArithmeticSequencePlugin $problemTemplatePlugin,
        PluginContainer $pluginContainer,
        StringsHelper $stringsHelper, ConstHelper $constHelper,
        ProblemTemplateSession $problemTemplateSession,
        ArithmeticSequenceTemplateFunctionality $arithmeticSequenceTemplateFunctionality
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
        $this->functionality = $arithmeticSequenceTemplateFunctionality;
        $this->problemTemplatePlugin = $problemTemplatePlugin;
    }

    /**
     * @param bool $edit
     * @return mixed
     */
    public function create(bool $edit = false)
    {
        return new ArithmeticSeqTemplateFormControl(
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