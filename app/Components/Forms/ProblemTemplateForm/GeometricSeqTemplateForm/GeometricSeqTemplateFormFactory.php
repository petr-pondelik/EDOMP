<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:56
 */

namespace ProblemTemplateForm\GeometricSeqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use App\Model\Persistent\Functionality\ProblemTemplate\GeometricSequenceTemplateFunctionality;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Plugins\GeometricSequencePlugin;
use App\Services\PluginContainer;
use App\Services\ProblemTemplateSession;
use App\Services\Validator;

/**
 * Class GeometricSeqTemplateFormFactory
 * @package ProblemTemplateForm\GeometricSeqTemplateForm
 */
class GeometricSeqTemplateFormFactory extends ProblemTemplateFormFactory
{
    /**
     * GeometricSeqTemplateFormFactory constructor.
     * @param Validator $validator
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param GeometricSequencePlugin $problemTemplatePlugin
     * @param PluginContainer $pluginContainer
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param ProblemTemplateSession $problemTemplateSession
     * @param GeometricSequenceTemplateFunctionality $geometricSequenceTemplateFunctionality
     */
    public function __construct
    (
        Validator $validator, DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository, ProblemConditionTypeRepository $problemConditionTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        GeometricSequencePlugin $problemTemplatePlugin,
        PluginContainer $pluginContainer,
        StringsHelper $stringsHelper, ConstHelper $constHelper,
        ProblemTemplateSession $problemTemplateSession,
        GeometricSequenceTemplateFunctionality $geometricSequenceTemplateFunctionality
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
        $this->functionality = $geometricSequenceTemplateFunctionality;
        $this->problemTemplatePlugin = $problemTemplatePlugin;
    }

    /**
     * @param bool $edit
     * @return mixed
     */
    public function create(bool $edit = false)
    {
        return new GeometricSeqTemplateFormControl(
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