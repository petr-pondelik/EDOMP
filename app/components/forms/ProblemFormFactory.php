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


    public function create(bool $generatableTypes = false)
    {
        $difficulties = $this->difficultyRepository->findAssoc([], "id");
        if(!$generatableTypes)
            $types = $this->problemTypeRepository->findAssoc([], "id");
        else
            $types = $this->problemTypeRepository->findAssoc([
                "is_generatable" => true
            ], "id");

        $subcategories = $this->subCategoryRepository->findAssoc([], "id");
        bdump($subcategories);

        $resultConditions = $this->problemConditionRepository->findAssoc([
            "problemConditionType.id" => $this->constHelper::RESULT
        ], "id");
        bdump($resultConditions);

        $discriminantConditions = $this->problemConditionRepository->findAssoc([
            "problemConditionType.id" => $this->constHelper::DISCRIMINANT
        ], "id");

        $form = parent::create();

        $form->addSelect('type', 'Typ', $types)
            ->setDefaultValue(1)
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('type');

        $form->addSelect("subcategory", "Podkategorie", $subcategories)
            ->setDefaultValue(1)
            ->setHtmlAttribute("class", "form-control");

        $form->addTextArea('before', 'Zadání před')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('before');

        $form->addTextArea('structure', 'Struktura')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('structure');

        $form->addText("variable", "Neznámá")
            ->setHtmlAttribute("class", "form-control")
            ->setHtmlId("variable");

        $form->addTextArea('after', 'Zadání po')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('after');

        $form->addSelect('difficulty', 'Obtížnost', $difficulties)
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('difficulty');

        //Arithmetic sequences
        /*$form->addInteger('first_n_arithmetic_seq', 'Prvních členů:')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('first-n-arithmetic-seq');*/

        //Geometric sequences
        /*$form->addInteger('first_n_geometric_seq', 'Prvních členů:')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('first-n-geometric-seq');*/

        //Conditions
        $form->addSelect('condition_' . $this->constHelper::RESULT, 'Podmínka výsledku', $resultConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::RESULT);

        $form->addHidden('condition_valid_'.$this->constHelper::RESULT)
            ->setDefaultValue(1);

        $form->addSelect('condition_' . $this->constHelper::DISCRIMINANT, 'Podmínka diskriminantu', $discriminantConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::DISCRIMINANT);

        //Field for storing all conditions final valid state (aggregation of conditions)
        $form->addHidden('conditions_valid')
            ->setDefaultValue(1)
            ->setHtmlId('conditions_valid');

        $form->addSubmit('prototype_create_submit', 'Vytvořit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        $form->addSubmit('submit', 'Vytvořit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;
    }

}