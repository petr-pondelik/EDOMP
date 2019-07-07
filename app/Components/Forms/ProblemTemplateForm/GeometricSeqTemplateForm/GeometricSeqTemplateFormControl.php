<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:56
 */

namespace ProblemTemplateForm\GeometricSeqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use App\Helpers\ConstHelper;
use App\Model\Functionality\BaseFunctionality;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Services\MathService;
use App\Services\ValidationService;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class GeometricSeqTemplateFormControl
 * @package ProblemTemplateForm\GeometricSeqTemplateForm
 */
class GeometricSeqTemplateFormControl extends ProblemTemplateFormControl
{
    /**
     * @var string
     */
    protected $type = 'GeometricSeqTemplateForm';

    /**
     * @var int
     */
    protected $typeId;

    /**
     * @var array
     */
    protected $baseItems = [
        'variable',
        'subCategory',
        'difficulty',
        'firstN'
    ];

    /**
     * GeometricSeqTemplateFormControl constructor.
     * @param ValidationService $validationService
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
        ValidationService $validationService, BaseFunctionality $functionality, DifficultyRepository $difficultyRepository,
        ProblemTypeRepository $problemTypeRepository, SubCategoryRepository $subCategoryRepository,
        ProblemConditionRepository $problemConditionRepository, MathService $mathService, ConstHelper $constHelper, bool $edit = false)
    {
        parent::__construct
        (
            $validationService, $functionality, $difficultyRepository, $problemTypeRepository, $subCategoryRepository,
            $problemConditionRepository, $mathService, $constHelper, $edit
        );
        $this->typeId = $this->constHelper::GEOMETRIC_SEQ;
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