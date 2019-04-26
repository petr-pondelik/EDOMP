<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.2.19
 * Time: 23:05
 */

namespace App\Components\Forms;

use App\Model\Managers\ConditionManager;
use App\Model\Managers\DifficultyManager;
use App\Model\Managers\ProblemTypeManager;
use App\Model\Managers\SubCategoryManager;
use App\Services\ValidationService;
use Nette\Application\UI\Form;
use App\Helpers\ConstHelper;
use Tracy\Debugger;

/**
 * Class ProblemFormFactory
 */
class ProblemFormFactory extends BaseForm
{
    /**
     * @var DifficultyManager
     */
    protected $difficultyManager;

    /**
     * @var SubCategoryManager
     */
    protected $subCategoryManager;

    /**
     * @var ProblemTypeManager
     */
    protected $problemTypeManager;

    /**
     * @var ConditionManager
     */
    protected $conditionManager;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemFormFactory constructor.
     * @param DifficultyManager $difficultyManager
     * @param SubCategoryManager $subCategoryManager
     * @param ProblemTypeManager $problemTypeManager
     * @param ConditionManager $conditionManager
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        DifficultyManager $difficultyManager, SubCategoryManager $subCategoryManager,
        ProblemTypeManager $problemTypeManager, ConditionManager $conditionManager,
        ConstHelper $constHelper
    )
    {
        $this->difficultyManager = $difficultyManager;
        $this->subCategoryManager = $subCategoryManager;
        $this->problemTypeManager = $problemTypeManager;
        $this->conditionManager = $conditionManager;
        $this->constHelper = $constHelper;
    }

    /**
     * @param bool $generatableTypes
     * @return Form
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function create(bool $generatableTypes = false)
    {
        $difficulties = $this->difficultyManager->getAll('ASC');
        if(!$generatableTypes)
            $types = $this->problemTypeManager->getAll('ASC');
        else
            $types = $this->problemTypeManager->getByCond("is_generatable = true");

        $subcategories = $this->subCategoryManager->getAll("ASC");

        $resultConditions = $this->conditionManager->getByCondType($this->constHelper::RESULT);
        $discriminantConditions = $this->conditionManager->getByCondType($this->constHelper::DISCRIMINANT);

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
        $form->addInteger('first_n_arithmetic_seq', 'Prvních členů:')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('first-n-arithmetic-seq');

        //Geometric sequences
        $form->addInteger('first_n_geometric_seq', 'Prvních členů:')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('first-n-geometric-seq');

        //Conditions
        $form->addSelect('condition_' . $this->constHelper::RESULT, 'Podmínka výsledku', $resultConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::RESULT);

        $form->addHidden('condition_valid_'.$this->constHelper::RESULT)
            ->setDefaultValue(1);

        $form->addSelect('condition_' . $this->constHelper::DISCRIMINANT, 'Podmínka diskriminantu', $discriminantConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::DISCRIMINANT);

        $form->addHidden('condition_valid_'.$this->constHelper::DISCRIMINANT)
            ->setDefaultValue(1);

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