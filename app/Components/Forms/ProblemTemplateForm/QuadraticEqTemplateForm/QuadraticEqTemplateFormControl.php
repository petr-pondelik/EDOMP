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
use App\Model\NonPersistent\ProblemTemplateNP;
use App\Model\NonPersistent\QuadraticEquationTemplateNP;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
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
     * @var string
     */
    protected $formName = 'QuadraticEqTemplateForm';

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
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addSelect('variable', 'Neznámá *', [
            'x' => 'x',
            'y' => 'y',
            'z' => 'z'
        ])
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Neznámá šablony.')
            ->setHtmlId('variable');

        return $form;
    }

    /**
     * @param ArrayHash $values
     * @return QuadraticEquationTemplateNP|mixed
     */
    protected function createNonPersistentEntity(ArrayHash $values): QuadraticEquationTemplateNP
    {
        return new QuadraticEquationTemplateNP($values);
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardize(ProblemTemplateNP $problemTemplate): ?ProblemTemplateNP
    {
        try{
            $standardized = $this->mathService->standardizeQuadraticEquation($problemTemplate);
        } catch (\Exception $e){
            $this['form']['body']->addError($e->getMessage());
            return null;
        }
        return $standardized;
    }
}