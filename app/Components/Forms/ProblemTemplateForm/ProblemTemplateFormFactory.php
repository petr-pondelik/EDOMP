<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 17:10
 */

namespace App\Components\Forms\ProblemTemplateForm;

use App\Components\Forms\FormFactory;
use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Plugins\ProblemPlugin;
use App\Services\PluginContainer;
use App\Services\ProblemTemplateSession;
use App\Services\ProblemTemplateState;
use App\Services\Validator;

/**
 * Class ProblemTemplateFormFactory
 * @package App\Components\Forms\ProblemTemplateForm
 */
abstract class ProblemTemplateFormFactory extends FormFactory
{
    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * @var ProblemPlugin
     */
    protected $problemTemplatePlugin;

    /**
     * @var PluginContainer
     */
    protected $pluginContainer;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var ProblemTemplateSession
     */
    protected $problemTemplateSession;

    /**
     * ProblemTemplateFormFactory constructor.
     * @param Validator $validator
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param PluginContainer $pluginContainer
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param ProblemTemplateSession $problemTemplateSession
     */
    public function __construct
    (
        Validator $validator,
        DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository, ProblemConditionRepository $problemConditionRepository,
        PluginContainer $pluginContainer, StringsHelper $stringsHelper, ConstHelper $constHelper,
        ProblemTemplateSession $problemTemplateSession
    )
    {
        parent::__construct($validator);
        $this->difficultyRepository = $difficultyRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->pluginContainer = $pluginContainer;
        $this->stringsHelper = $stringsHelper;
        $this->constHelper = $constHelper;
        $this->problemTemplateSession = $problemTemplateSession;
    }

    /**
     * @param BaseFunctionality $functionality
     * @param bool $edit
     * @return mixed
     */
    abstract public function create(BaseFunctionality $functionality, bool $edit = false);
}