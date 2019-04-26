<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.3.19
 * Time: 18:22
 */

namespace App\Components\Forms;

use App\Model\Managers\DifficultyManager;
use App\Model\Managers\GroupManager;
use App\Model\Managers\LogoManager;
use App\Model\Managers\ProblemManager;
use App\Model\Managers\ProblemTypeManager;
use App\Model\Managers\SpecializationManager;
use App\Model\Managers\SubCategoryManager;
use App\Model\Managers\TestTermManager;
use Nette;

/**
 * Class TestFormFactory
 * @package app\components\forms
 */
class TestFormFactory extends BaseForm
{

    /**
     * @var ProblemManager
     */
    protected $problemManager;

    /**
     * @var ProblemTypeManager
     */
    protected $problemTypeManager;

    /**
     * @var DifficultyManager
     */
    protected $difficultyManager;

    /**
     * @var SpecializationManager
     */
    protected $specializationManager;

    /**
     * @var GroupManager
     */
    protected $groupManager;

    /**
     * @var TestTermManager
     */
    protected $testTermManager;

    /**
     * @var SubCategoryManager
     */
    protected $subCategoryManager;

    /**
     * @var LogoManager
     */
    protected $logoManager;

    /**
     * TestFormFactory constructor.
     * @param ProblemManager $problemManager
     * @param ProblemTypeManager $problemTypeManager
     * @param DifficultyManager $difficultyManager
     * @param SpecializationManager $specializationManager
     * @param GroupManager $groupManager
     * @param TestTermManager $testTermManager
     * @param SubCategoryManager $subCategoryManager
     * @param LogoManager $logoManager
     */
    public function __construct(
        ProblemManager $problemManager, ProblemTypeManager $problemTypeManager, DifficultyManager $difficultyManager,
        SpecializationManager $specializationManager, GroupManager $groupManager, TestTermManager $testTermManager,
        SubCategoryManager $subCategoryManager, LogoManager $logoManager
    )
    {
        $this->problemManager = $problemManager;
        $this->problemTypeManager = $problemTypeManager;
        $this->difficultyManager = $difficultyManager;
        $this->specializationManager = $specializationManager;
        $this->groupManager = $groupManager;
        $this->testTermManager = $testTermManager;
        $this->subCategoryManager = $subCategoryManager;
        $this->logoManager = $logoManager;
    }

    /**
     * @return Nette\Application\UI\Form
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function create()
    {
        $form = parent::create();

        $problemTypes = $this->problemTypeManager->getAllPairs('ASC', true);
        $difficulties = $this->difficultyManager->getAllPairs('ASC', true);
        $groups = $this->groupManager->getAllPairs('ASC');
        $testTerms = $this->testTermManager->getAllPairs('ASC');
        $subCategories = $this->subCategoryManager->getAllPairs("ASC", true);

        $form->addSelect('variants', 'Počet variant', [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8
        ])
            ->setHtmlAttribute('class', 'form-control col-12')
            ->setDefaultValue(true);

        $form->addHidden('problems_cnt')->setDefaultValue(1)
            ->setHtmlId('problemsCnt');

        $form->addText("logo_file", "Logo")
            ->setHtmlAttribute("class", "form-control")
            ->setHtmlId("test-logo-label")
            ->setDisabled();

        $form->addHidden('logo_file_hidden')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId("test-logo-id");

        $form->addSelect('group', 'Skupina', $groups)
            ->setHtmlAttribute('class', 'form-control');

        $form->addSelect("test_term", "Období", $testTerms)
            ->setHtmlAttribute("class", "form-control");

        $form->addText("school_year", "Školní rok")
            ->setHtmlAttribute("class", "form-control");

        $form->addInteger("test_number", "Číslo testu")
            ->setHtmlAttribute("class", "form-control");

        $form->addTextArea("introduction_text", "Úvodní text")
            ->setHtmlAttribute("class", "form-control");

        for($i = 0; $i < 20; $i++) {

            $form->addSelect('is_prototype_'.$i, 'Šablona', [
                -1 => 'Bez omezení',
                1 => 'Ano',
                0 => 'Ne'
            ])
                ->setHtmlAttribute('class', 'form-control filter')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'is_prototype')
                ->setHtmlId('is_prototype_'.$i);

            $form->addSelect("sub_category_id_" . $i, "Sub-Kategorie", $subCategories)
                ->setHtmlAttribute("class", "form-control filter")
                ->setHtmlAttribute("data-problem-id", $i)
                ->setHtmlAttribute("data-filter-type", "sub_category_id")
                ->setHtmlId("problem_type_id_" . $i);

            $form->addSelect('problem_type_id_' . $i, 'Typ', $problemTypes)
                ->setHtmlAttribute('class', 'form-control filter')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'problem_type_id')
                ->setHtmlId('problem_type_id_'.$i);

            $form->addSelect('difficulty_id_'.$i, 'Obtížnost', $difficulties)
                ->setHtmlAttribute('class', 'form-control filter')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'difficulty_id')
                ->setHtmlId('difficulty_id_'.$i);

            $form->addSelect('problem_'.$i, 'Úloha', $this->problemManager->getAll())
                ->setHtmlAttribute('class', 'form-control problem-select')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlId('problem_'.$i);

            $form->addCheckbox("newpage_" . $i, "Nová stránka");

        }

        $form->addSubmit('create', 'Vytvořit')
            ->setHtmlAttribute('class', 'btn btn-primary col-12');

        return $form;
    }

}