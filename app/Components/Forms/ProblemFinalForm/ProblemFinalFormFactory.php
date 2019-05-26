<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 14:51
 */

namespace App\Components\Forms\ProblemFinalForm;


use App\Components\Forms\BaseFormFactory;
use App\Helpers\ConstHelper;
use App\Model\Functionality\ProblemFinalFunctionality;
use App\Model\Functionality\ProblemFunctionality;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Services\ValidationService;

/**
 * Class ProblemFinalFormFactory
 * @package App\Components\Forms\ProblemFinalForm
 */
class ProblemFinalFormFactory extends BaseFormFactory
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
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemFinalFormFactory constructor.
     * @param ValidationService $validationService
     * @param ProblemFinalFunctionality $problemFinalFunctionality
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        ValidationService $validationService,
        ProblemFinalFunctionality $problemFinalFunctionality,
        DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository, ProblemConditionRepository $problemConditionRepository,
        ConstHelper $constHelper
    )
    {
        parent::__construct($validationService);
        $this->functionality = $problemFinalFunctionality;
        $this->difficultyRepository = $difficultyRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->constHelper = $constHelper;
    }

    /**
     * @param bool $edit
     * @return ProblemFinalFormControl
     */
    public function create(bool $edit = false): ProblemFinalFormControl
    {
        return new ProblemFinalFormControl
        (
            $this->validationService, $this->functionality, $this->difficultyRepository, $this->problemTypeRepository,
            $this->subCategoryRepository, $this->problemConditionRepository, $this->constHelper, $edit
        );
    }
}