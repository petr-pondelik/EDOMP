<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 11:34
 */

namespace App\Components\Forms;

use App\Helpers\ConstHelper;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Service\ValidationService;
use Nette\Application\UI\Form;

/**
 * Class TemplateFormFactory
 * @package App\Components\Forms
 */
class TemplateFormFactory extends BaseForm
{
    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var
     */
    protected $mathService;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemFormFactory constructor.
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        DifficultyRepository $difficultyRepository, SubCategoryRepository $subCategoryRepository,
        ProblemConditionRepository $problemConditionRepository,
        ConstHelper $constHelper
    )
    {
        $this->difficultyRepository = $difficultyRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->constHelper = $constHelper;
    }

    /**
     * @param int $templateType
     * @param bool $edit
     * @return Form
     * @throws \Exception
     */
    public function create(int $templateType = 0, bool $edit = false): Form
    {
        $difficulties = $this->difficultyRepository->findAssoc([], "id");
        $subcategories = $this->subCategoryRepository->findAssoc([], "id");

        $resultConditions = $this->problemConditionRepository->findAssoc([
            "problemConditionType.id" => $this->constHelper::RESULT
        ], "accessor");

        $discriminantConditions = $this->problemConditionRepository->findAssoc([
            "problemConditionType.id" => $this->constHelper::DISCRIMINANT
        ], "accessor");

        $form = parent::create();

        if($edit){
            $form->addHidden("id_hidden");
            $form->addInteger("id", "ID")
                ->setHtmlAttribute("class", "form-control")
                ->setDisabled();
        }

        $form->addHidden("type")
                ->setDefaultValue($templateType);

        $form->addSelect("subcategory", "Podkategorie", $subcategories)
            ->setDefaultValue(1)
            ->setHtmlAttribute("class", "form-control");

        $form->addTextArea('text_before', 'Zadání před')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('before');

        $form->addTextArea('body', 'Tělo')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('body');

        $form->addText("variable", "Neznámá")
            ->setHtmlAttribute("class", "form-control")
            ->setHtmlId("variable");

        $form->addTextArea('text_after', 'Zadání po')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('after');

        $form->addSelect('difficulty', 'Obtížnost', $difficulties)
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('difficulty');

        if($templateType === $this->constHelper::ARITHMETIC_SEQ || $templateType === $this->constHelper::GEOMETRIC_SEQ){
            $form->addInteger('first_n', 'Prvních členů:')
                ->setHtmlAttribute('class', 'form-control')
                ->setHtmlId('first-n');
        }

        //Conditions
        $form->addSelect('condition_' . $this->constHelper::RESULT, 'Podmínka výsledku', $resultConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::RESULT);

        $form->addSelect('condition_' . $this->constHelper::DISCRIMINANT, 'Podmínka diskriminantu', $discriminantConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::DISCRIMINANT);

        //Field for storing all conditions final valid state
        $form->addHidden('conditions_valid')
            ->setDefaultValue(1)
            ->setHtmlId('conditions_valid');

        $form->addSubmit('submit', 'Vytvořit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;
    }
}