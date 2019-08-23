<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 14:51
 */

namespace App\Components\Forms\ProblemFinalForm;


use App\Components\Forms\FormFactory;
use App\Helpers\ConstHelper;
use App\Model\Persistent\Functionality\ProblemFinalFunctionality;
use App\Model\Persistent\Functionality\ProblemFunctionality;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Services\Validator;

/**
 * Class ProblemFinalFormFactory
 * @package App\Components\Forms\ProblemFinalForm
 */
class ProblemFinalFormFactory extends FormFactory
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
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemFinalFormFactory constructor.
     * @param Validator $validator
     * @param ProblemFinalFunctionality $problemFinalFunctionality
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        Validator $validator,
        ProblemFinalFunctionality $problemFinalFunctionality,
        DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository, ProblemConditionRepository $problemConditionRepository,
        ConstHelper $constHelper
    )
    {
        parent::__construct($validator);
        $this->functionality = $problemFinalFunctionality;
        $this->difficultyRepository = $difficultyRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
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
            $this->validator, $this->functionality, $this->difficultyRepository, $this->problemTypeRepository,
            $this->subCategoryRepository, $this->problemConditionTypeRepository,
            $this->problemConditionRepository, $this->constHelper, $edit
        );
    }
}