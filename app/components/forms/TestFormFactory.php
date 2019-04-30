<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.3.19
 * Time: 18:22
 */

namespace App\Components\Forms;

use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TermRepository;
use Nette;

/**
 * Class TestFormFactory
 * @package app\components\forms
 */
class TestFormFactory extends BaseForm
{

    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var TermRepository
     */
    protected $termRepository;

    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var LogoRepository
     */
    protected $logoRepository;


    /**
     * TestFormFactory constructor.
     * @param ProblemRepository $problemRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param DifficultyRepository $difficultyRepository
     * @param GroupRepository $groupRepository
     * @param TermRepository $termRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param LogoRepository $logoRepository
     */
    public function __construct(
        ProblemRepository $problemRepository,
        ProblemTypeRepository $problemTypeRepository, DifficultyRepository $difficultyRepository,
        GroupRepository $groupRepository, TermRepository $termRepository,
        SubCategoryRepository $subCategoryRepository, LogoRepository $logoRepository
    )
    {
        $this->problemRepository = $problemRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->groupRepository = $groupRepository;
        $this->termRepository = $termRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->logoRepository = $logoRepository;
    }

    /**
     * @return Nette\Application\UI\Form
     * @throws \Exception
     */
    public function create()
    {
        $form = parent::create();

        $problemTypes = $this->problemTypeRepository->findAssoc([], "id");
        $difficulties = $this->difficultyRepository->findAssoc([], "id");
        $groups = $this->groupRepository->findAssoc([],"id");
        $testTerms = $this->termRepository->findAssoc([],"id");
        $subCategories = $this->subCategoryRepository->findAssoc([], "id");

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

            $form->addSelect('is_template_'.$i, 'Šablona', [
                -1 => "Bez podmínky",
                1 => 'Ano',
                0 => 'Ne'
            ])
                ->setHtmlAttribute('class', 'form-control filter')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'is_template')
                ->setHtmlId('is_template_'.$i);

            $form->addSelect("sub_category_id_" . $i, "Téma",
                array_merge([-1 => "Bez podmínky"], $subCategories)
            )
                ->setHtmlAttribute("class", "form-control filter")
                ->setHtmlAttribute("data-problem-id", $i)
                ->setHtmlAttribute("data-filter-type", "sub_category_id")
                ->setHtmlId("sub_category_id_" . $i);

            $form->addSelect('problem_type_id_' . $i, 'Typ',
                array_merge([-1 => "Bez podmínky"], $problemTypes)
            )
                ->setHtmlAttribute('class', 'form-control filter')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'problem_type_id')
                ->setHtmlId('problem_type_id_'.$i);

            $form->addSelect('difficulty_id_'.$i, 'Obtížnost',
                array_merge( [-1 => "Bez podmínky"], $difficulties)
            )
                ->setHtmlAttribute('class', 'form-control filter')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'difficulty_id')
                ->setHtmlId('difficulty_id_'.$i);

            //bdump(array_merge($this->problemTemplateRepository->findAssoc([], "id"), $this->problemRepository->findAssoc([], "id")));

            $form->addSelect('problem_'.$i, 'Úloha', $this->problemRepository->findAssoc([], "id"))
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