<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:39
 */

namespace App\Components\Forms\TestForm;


use App\Components\Forms\BaseFormControl;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TermRepository;
use App\Model\Repository\TestRepository;
use App\Service\FileService;
use App\Service\TestBuilderService;
use App\Service\ValidationService;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

/**
 * Class TestFormControl
 * @package App\Components\Forms\TestForm
 */
class TestFormControl extends BaseFormControl
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var TestRepository
     */
    protected $testRepository;

    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var ProblemTemplateRepository
     */
    protected $problemTemplateRepository;

    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var TermRepository
     */
    protected $termRepository;

    /**
     * @var TestBuilderService
     */
    protected $testBuilderService;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * TestFormControl constructor.
     * @param ValidationService $validationService
     * @param EntityManager $entityManager
     * @param TestRepository $testRepository
     * @param ProblemRepository $problemRepository
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param DifficultyRepository $difficultyRepository
     * @param LogoRepository $logoRepository
     * @param GroupRepository $groupRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param TermRepository $termRepository
     * @param TestBuilderService $testBuilderService
     * @param FileService $fileService
     * @param bool $edit
     * @param bool $super
     */
    public function __construct
    (
        ValidationService $validationService, EntityManager $entityManager,
        TestRepository $testRepository,
        ProblemRepository $problemRepository, ProblemTemplateRepository $problemTemplateRepository, ProblemTypeRepository $problemTypeRepository,
        DifficultyRepository $difficultyRepository, LogoRepository $logoRepository, GroupRepository $groupRepository,
        SubCategoryRepository $subCategoryRepository, TermRepository $termRepository,
        TestBuilderService $testBuilderService, FileService $fileService,
        bool $edit = false, bool $super = false
    )
    {
        parent::__construct($validationService, $edit, $super);
        $this->entityManager = $entityManager;
        $this->testRepository = $testRepository;
        $this->problemRepository =$problemRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->logoRepository = $logoRepository;
        $this->groupRepository = $groupRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->termRepository = $termRepository;
        $this->testBuilderService = $testBuilderService;
        $this->fileService = $fileService;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

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

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->getValues();
        $validateFields["logo_file"] = $values->logo_file_hidden;
        $validateFields["school_year"] = $values->school_year;
        $validateFields["test_number"] = $values->test_number;
        $validationErrors = $this->validationService->validate($validateFields);
        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
        }
        $this->redrawControl("logoFileErrorSnippet");
        $this->redrawControl("schoolYearErrorSnippet");
        $this->redrawControl("testNumberErrorSnippet");
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $testData = $this->testBuilderService->buildTest($values);
        }
        catch(\Exception $e){
            $this->onError($e);
            return;
        }
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/template/export.latte');
        try{
            $this->entityManager->flush();
        } catch (\Exception $e){
            $this->onError($e);
        }
        $test = $this->testRepository->find($testData->testId);
        foreach($testData->variants as $variant){
            $template->variant = $variant;
            $template->test = $test;
            FileSystem::createDir( DATA_DIR . '/tests/' . $testData->testId);
            file_put_contents( DATA_DIR . '/tests/' . $testData->testId . '/variant_' . Strings::lower($variant) . '.tex', (string) $template);
        }
        $this->fileService->createTestZip($testData->testId);
        $this->onSuccess();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void {}

    public function handleFilterChange(array $filters): void
    {
        foreach($filters as $problemKey => $problemFilters){

            if(!isset($problemFilters["filters"]["is_template"]) || $problemFilters["filters"]["is_template"])
                $filterRes = $this->problemTemplateRepository->findFiltered($problemFilters["filters"]);
            else
                $filterRes = $this->problemRepository->findFiltered($problemFilters["filters"]);

            if(isset($problemFilters['filters'])){
                foreach ($problemFilters['filters'] as $filterType => $filterVal) {
                    $this['form'][$filterType . '_' . $problemKey]->setValue($filterVal);
                }
            }

            $this['form']['problem_' . $problemKey]->setItems($filterRes);

            if(array_key_exists($problemFilters['selected'], $filterRes))
                $this['form']['problem_' . $problemKey]->setValue($problemFilters['selected']);

        }

        $this->redrawControl('testCreateFormSnippet');
    }

    public function render(): void
    {
        $this->template->logos = $this->logoRepository->findBy([], ["id" => "DESC"]);
        $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . 'template/create.latte');
    }
}