<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.2.19
 * Time: 23:05
 */

namespace App\Components\Forms;

use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Service\ValidationService;
use Nette\Application\UI\Form;
use App\Helpers\ConstHelper;

/**
 * Class ProblemFormFactory
 */
class ProblemFormFactory extends BaseForm
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
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemFormFactory constructor.
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        DifficultyRepository $difficultyRepository, SubCategoryRepository $subCategoryRepository,
        ProblemTypeRepository $problemTypeRepository, ProblemConditionRepository $problemConditionRepository,
        ConstHelper $constHelper
    )
    {
        $this->difficultyRepository = $difficultyRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->constHelper = $constHelper;
    }

    /**
     * @param bool $generatableTypes
     * @return Form
     * @throws \Exception
     */
    public function create(bool $generatableTypes = false): Form
    {
        $difficulties = $this->difficultyRepository->findAssoc([], "id");
        if(!$generatableTypes)
            $types = $this->problemTypeRepository->findAssoc([], "id");
        else
            $types = $this->problemTypeRepository->findAssoc([
                "is_generatable" => true
            ], "id");

        $subcategories = $this->subCategoryRepository->findAssoc([], "id");

        $resultConditions = $this->problemConditionRepository->findAssoc([
            "problemConditionType.id" => $this->constHelper::RESULT
        ], "accessor");

        $discriminantConditions = $this->problemConditionRepository->findAssoc([
            "problemConditionType.id" => $this->constHelper::DISCRIMINANT
        ], "accessor");

        $form = parent::create();

        $form->addSelect('type', 'Typ', $types)
            ->setDefaultValue(1)
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('type');

        $form->addSelect("subcategory", "Podkategorie", $subcategories)
            ->setDefaultValue(1)
            ->setHtmlAttribute("class", "form-control");

        $form->addTextArea('text_before', 'Zadání před')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('before');

        $form->addTextArea('body', 'Tělo')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('structure');

        $form->addText("variable", "Neznámá")
            ->setHtmlAttribute("class", "form-control")
            ->setHtmlId("variable");

        $form->addTextArea('text_after', 'Zadání po')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('after');

        $form->addSelect('difficulty', 'Obtížnost', $difficulties)
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('difficulty');

        //Conditions
        $form->addSelect('condition_' . $this->constHelper::RESULT, 'Podmínka výsledku', $resultConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::RESULT);

        $form->addSelect('condition_' . $this->constHelper::DISCRIMINANT, 'Podmínka diskriminantu', $discriminantConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::DISCRIMINANT);

        $form->addSubmit('prototype_create_submit', 'Vytvořit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        $form->addSubmit('submit', 'Vytvořit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;
    }

}