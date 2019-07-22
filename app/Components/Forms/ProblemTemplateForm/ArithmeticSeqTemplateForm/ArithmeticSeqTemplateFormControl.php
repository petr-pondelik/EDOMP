<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:58
 */

namespace App\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use App\Helpers\ConstHelper;
use App\Model\Functionality\BaseFunctionality;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Services\MathService;
use App\Services\Validator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class ArithmeticSeqTemplateFormControl
 * @package App\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm
 */
class ArithmeticSeqTemplateFormControl extends ProblemTemplateFormControl
{
    /**
     * @var string
     */
    protected $type = 'ArithmeticSeqTemplateForm';

    /**
     * @var int
     */
    protected $typeId;

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
        ],
        [
            'field' => 'firstN',
            'validation' => 'notEmptyPositive'
        ]
    ];

    /**
     * ArithmeticSeqTemplateFormControl constructor.
     * @param Validator $validator
     * @param BaseFunctionality $functionality
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param MathService $mathService
     * @param ConstHelper $constHelper
     * @param bool $edit
     */
    public function __construct
    (
        Validator $validator, BaseFunctionality $functionality, DifficultyRepository $difficultyRepository,
        ProblemTypeRepository $problemTypeRepository, SubCategoryRepository $subCategoryRepository,
        ProblemConditionRepository $problemConditionRepository, MathService $mathService,
        ConstHelper $constHelper, bool $edit = false
    )
    {
        parent::__construct
        (
            $validator, $functionality, $difficultyRepository, $problemTypeRepository, $subCategoryRepository,
            $problemConditionRepository, $mathService, $constHelper, $edit
        );
        $this->typeId = $this->constHelper::ARITHMETIC_SEQ;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addInteger('firstN', 'Počet prvních členů *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte počet zkoumaných prvních členů.')
            ->setHtmlId('first-n');

        $form['type']->setDefaultValue($this->typeId);

        return $form;
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
            'problemType' => $this->typeId,
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
            $standardized = $this->mathService->standardizeSequence($values->body);
        } catch (\Exception $e){
            $this['form']['body']->addError($e->getMessage());
            return null;
        }
        return $standardized;
    }
}