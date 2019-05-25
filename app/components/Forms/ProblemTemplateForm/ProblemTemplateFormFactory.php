<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 17:10
 */

namespace App\Components\Forms\ProblemTemplateForm;

use App\Components\Forms\BaseFormFactory;
use App\Helpers\ConstHelper;
use App\Model\Functionality\BaseFunctionality;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Service\MathService;
use App\Service\ValidationService;

/**
 * Class ProblemTemplateFormFactory
 * @package App\Components\Forms\ProblemTemplateForm
 */
class ProblemTemplateFormFactory extends BaseFormFactory
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
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * @var MathService
     */
    protected $mathService;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemTemplateFormFactory constructor.
     * @param ValidationService $validationService
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param MathService $mathService
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        ValidationService $validationService,
        DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository, ProblemConditionRepository $problemConditionRepository,
        MathService $mathService, ConstHelper $constHelper
    )
    {
        parent::__construct($validationService);
        $this->difficultyRepository = $difficultyRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->mathService = $mathService;
        $this->constHelper = $constHelper;
    }

    /**
     * @param BaseFunctionality $functionality
     * @param int $templateType
     * @param bool $edit
     * @return ProblemTemplateFormControl
     */
    public function create(BaseFunctionality $functionality, int $templateType = 0, bool $edit = false): ProblemTemplateFormControl
    {
        return new ProblemTemplateFormControl(
            $this->validationService, $functionality, $this->difficultyRepository, $this->problemTypeRepository,
            $this->subCategoryRepository, $this->problemConditionRepository, $this->mathService, $this->constHelper, $templateType, $edit
        );
    }
}