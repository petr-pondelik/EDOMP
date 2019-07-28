<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:46
 */

namespace App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm;

use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use App\Helpers\ConstHelper;
use App\Model\Functionality\BaseFunctionality;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemConditionTypeRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Services\MathService;
use App\Services\Validator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class QuadraticEqTemplateFormControl
 * @package App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm
 */
class QuadraticEqTemplateFormControl extends ProblemTemplateFormControl
{
    /**
     * @var array
     */
    protected $baseItems = [
        [
            'field' => 'variable',
            'validation' => 'variable'
        ],
        [
            'field' => 'subCategory',
            'validation' => 'notEmpty'
        ],
        [
            'field' => 'difficulty',
            'validation' => 'notEmpty'
        ]
    ];

    /**
     * @var array
     */
    protected $baseItemsCondition = [
        [
            'field' => 'variable',
            'validation' => 'variable'
        ]
    ];

    /**
     * QuadraticEqTemplateFormControl constructor.
     * @param Validator $validator
     * @param BaseFunctionality $functionality
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param MathService $mathService
     * @param ConstHelper $constHelper
     * @param bool $edit
     */
    public function __construct
    (
        Validator $validator, BaseFunctionality $functionality, DifficultyRepository $difficultyRepository,
        ProblemTypeRepository $problemTypeRepository, SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository, ProblemConditionRepository $problemConditionRepository,
        MathService $mathService, ConstHelper $constHelper, bool $edit = false
    )
    {
        parent::__construct
        (
            $validator, $functionality, $difficultyRepository, $problemTypeRepository, $subCategoryRepository,
            $problemConditionTypeRepository, $problemConditionRepository, $mathService, $constHelper, $edit
        );
        $this->attachEntities($this->constHelper::QUADRATIC_EQ);
    }

    /**
     * @param ArrayHash $values
     * @return ArrayHash
     */
    public function collectBodyValidationData(ArrayHash $values): ArrayHash
    {
        return ArrayHash::from([
            'body' => $values->body,
            'variable' => $values->variable,
            'bodyType' => $this->constHelper::BODY_TEMPLATE
        ]);
    }

    /**
     * @param ArrayHash $values
     * @return mixed|string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardize(ArrayHash $values)
    {
        try{
            $standardized = $this->mathService->standardizeQuadraticEquation($values->body);
        } catch (\Exception $e){
            $this['form']['body']->addError($e->getMessage());
            return null;
        }
        return $standardized;
    }
}