<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:39
 */

namespace App\Components\Forms\TestForm;


use App\Components\Forms\FormControl;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TestRepository;
use App\Services\FileService;
use App\Services\TestBuilderService;
use App\Services\ValidationService;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

/**
 * Class TestFormControl
 * @package App\Components\Forms\TestForm
 */
class TestFormControl extends FormControl
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
     * @var ProblemFinalRepository
     */
    protected $problemFinalRepository;

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
     * @param ProblemFinalRepository $problemFinalRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param DifficultyRepository $difficultyRepository
     * @param LogoRepository $logoRepository
     * @param GroupRepository $groupRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param TestBuilderService $testBuilderService
     * @param FileService $fileService
     */
    public function __construct
    (
        ValidationService $validationService, EntityManager $entityManager,
        TestRepository $testRepository,
        ProblemRepository $problemRepository, ProblemTemplateRepository $problemTemplateRepository, ProblemFinalRepository $problemFinalRepository,
        ProblemTypeRepository $problemTypeRepository,
        DifficultyRepository $difficultyRepository, LogoRepository $logoRepository, GroupRepository $groupRepository,
        SubCategoryRepository $subCategoryRepository,
        TestBuilderService $testBuilderService, FileService $fileService
    )
    {
        parent::__construct($validationService);
        $this->entityManager = $entityManager;
        $this->testRepository = $testRepository;
        $this->problemRepository =$problemRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemFinalRepository = $problemFinalRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->logoRepository = $logoRepository;
        $this->groupRepository = $groupRepository;
        $this->subCategoryRepository = $subCategoryRepository;
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

        $problemTypes = $this->problemTypeRepository->findAssoc([], 'id');
        $difficulties = $this->difficultyRepository->findAssoc([], 'id');
        $groups = $this->groupRepository->findAllowed($this->presenter->user);
        $subCategories = $this->subCategoryRepository->findAssoc([], 'id');

        $form->addSelect('variants', 'Počet variant *', [
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

        $form->addText('logo_file', 'Logo *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Rozbalením a kliknutím zvolte logo z nabídky níže.')
            ->setHtmlId('test-logo-label')
            ->setDisabled();

        $form->addHidden('logo_file_hidden')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('test-logo-id');

        $form->addMultiSelect('groups', 'Skupiny *', $groups)
            ->setHtmlAttribute('class', 'form-control selectpicker')
            ->setHtmlAttribute('title', 'Zvolte skupiny.');

        $form->addText('test_term', 'Období *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte období ve školním roce.');

        $form->addText('school_year', 'Školní rok *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'rrrr/rr(rr) nebo rrrr-rr(rr)');

        $form->addInteger('test_number', 'Číslo testu *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte číslo testu.');

        $form->addTextArea('introduction_text', 'Úvodní text')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte úvodní text testu.');
        // Úvodní text se zobrazí pod hlavičkou testu

        for($i = 0; $i < 20; $i++) {

            $form->addSelect('is_template_'.$i, 'Šablona', [
                -1 => 'Bez podmínky',
                1 => 'Ano',
                0 => 'Ne'
            ])
                ->setHtmlAttribute('class', 'form-control filter')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'is_template')
                ->setHtmlId('is_template_'.$i);

            $form->addSelect('sub_category_id_' . $i, 'Téma',
                array_merge([-1 => 'Bez podmínky'], $subCategories)
            )
                ->setHtmlAttribute('class', 'form-control filter')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'sub_category_id')
                ->setHtmlId('sub_category_id_' . $i);

            $form->addSelect('problem_type_id_' . $i, 'Typ',
                array_merge([-1 => 'Bez podmínky'], $problemTypes)
            )
                ->setHtmlAttribute('class', 'form-control filter')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'problem_type_id')
                ->setHtmlId('problem_type_id_'.$i);

            $form->addSelect('difficulty_id_'.$i, 'Obtížnost',
                array_merge( [-1 => 'Bez podmínky'], $difficulties)
            )
                ->setHtmlAttribute('class', 'form-control filter')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'difficulty_id')
                ->setHtmlId('difficulty_id_'.$i);

            $problems[0] = 'Zvolit náhodně';
            $foundProblems = $this->problemRepository->findAssoc([], 'id');
            foreach ($foundProblems as $key => $item)
                $problems[$key]  = $item;

            $form->addSelect('problem_'.$i, 'Úloha', $problems)
                ->setHtmlAttribute('class', 'form-control problem-select')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlId('problem_'.$i);

            $form->addCheckbox('newpage_' . $i, 'Nová stránka');

        }

        $form['submit']->caption = 'Vytvořit';
        $form['submit']->setHtmlAttribute('class', 'btn btn-primary col-12');
        $form->onSuccess[] = [$this, 'handleFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->getValues();
        $validateFields['logo_file'] = $values->logo_file_hidden;
        $validateFields['groups'] = ArrayHash::from($values->groups);
        $validateFields['school_year'] = $values->school_year;
        $validateFields['test_number'] = $values->test_number;
        $validateFields['test_term'] = $values->test_term;
        $validationErrors = $this->validationService->validate($validateFields);
        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error){
                    $form[$veKey]->addError($error);
                }
            }
        }
        $this->redrawControl('logoFileErrorSnippet');
        $this->redrawControl('groupsErrorSnippet');
        $this->redrawControl('schoolYearErrorSnippet');
        $this->redrawControl('testNumberErrorSnippet');
        $this->redrawControl('testTermErrorSnippet');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $testData = $this->testBuilderService->buildTest($values);
        }
        catch(\Exception $e){
            $this->onError($e);
            return;
        }
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/templates/export.latte');
        try{
            $this->entityManager->flush();
        } catch (\Exception $e){
            $this->onError($e);
        }
        foreach($testData->variants as $variant){
            $template->variant = $variant;
            $template->test = $testData->test;
            FileSystem::createDir( DATA_DIR . '/tests/' . $testData->testId);
            file_put_contents( DATA_DIR . '/tests/' . $testData->testId . '/variant_' . Strings::lower($variant) . '.tex', (string) $template);
        }
        $this->fileService->createTestZip($testData->testId);
        $this->onSuccess();
    }

    /**
     * @param array $filters
     */
    public function handleFilterChange(array $filters): void
    {
        foreach($filters as $problemKey => $problemFilters){

            if(!isset($problemFilters['filters']['is_template']) || $problemFilters['filters']['is_template'] == -1){
                unset($problemFilters['filters']['is_template']);
                $filterRes = $this->problemRepository->findFiltered($problemFilters['filters']);
            }
            else if($problemFilters['filters']['is_template']){
                $filterRes = $this->problemTemplateRepository->findFiltered($problemFilters['filters']);
            }
            else{
                $filterRes = $this->problemFinalRepository->findFiltered($problemFilters['filters']);
            }

            if(isset($problemFilters['filters'])){
                foreach ($problemFilters['filters'] as $filterType => $filterVal) {
                    $this['form'][$filterType . '_' . $problemKey]->setValue($filterVal);
                }
            }

            $filterRes[0] = 'Zvolit náhodně';

            $this['form']['problem_' . $problemKey]->setItems($filterRes);

            if(array_key_exists($problemFilters['selected'], $filterRes)){
                $this['form']['problem_' . $problemKey]->setValue($problemFilters['selected']);
            }

        }

        $this->redrawControl('testCreateFormSnippet');
    }

    public function render(): void
    {
        $this->template->logos = $this->logoRepository->findBy([], ['id' => 'DESC']);
        $this->template->render(__DIR__ . '/templates/create.latte');
    }
}