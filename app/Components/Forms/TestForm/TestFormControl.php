<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:39
 */

namespace App\Components\Forms\TestForm;


use App\Arguments\ValidatorArgument;
use App\Components\Forms\EntityFormControl;
use App\Model\Persistent\Functionality\TestFunctionality;
use App\Model\Persistent\Repository\GroupRepository;
use App\Model\Persistent\Repository\LogoRepository;
use App\Services\FileService;
use App\Services\TestGeneratorService;
use App\Services\Validator;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;

/**
 * Class TestFormControl
 * @package App\Components\Forms\TestForm
 */
abstract class TestFormControl extends EntityFormControl
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var TestGeneratorService
     */
    protected $testGeneratorService;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * TestFormControl constructor.
     * @param Validator $validator
     * @param EntityManager $entityManager
     * @param LogoRepository $logoRepository
     * @param GroupRepository $groupRepository
     * @param TestGeneratorService $testGeneratorService
     * @param FileService $fileService
     * @param TestFunctionality $testFunctionality
     */
    public function __construct
    (
        Validator $validator, EntityManager $entityManager,
        LogoRepository $logoRepository, GroupRepository $groupRepository,
        TestGeneratorService $testGeneratorService, FileService $fileService,
        TestFunctionality $testFunctionality
    )
    {
        parent::__construct($validator);

        $this->entityManager = $entityManager;
        $this->logoRepository = $logoRepository;
        $this->groupRepository = $groupRepository;
        $this->testGeneratorService = $testGeneratorService;
        $this->fileService = $fileService;

        $this->functionality = $testFunctionality;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $groups = $this->groupRepository->findAllowed($this->presenter->user);
        $logos = $this->logoRepository->findAssoc([], 'id');

        $form->addSelect('variantsCnt', 'Počet variant *', [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8
        ])
            ->setHtmlAttribute('class', 'form-control col-12 selectpicker')
            ->setDefaultValue(true);

        $form->addHidden('problemsPerVariant')->setDefaultValue(1)
            ->setHtmlId('problemsPerVariant');

        $form->addSelect('logo', 'Logo *', $logos)
            ->setPrompt('Zvolte logo')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('test-logo');

        $form->addMultiSelect('groups', 'Skupiny *', $groups)
            ->setHtmlAttribute('class', 'form-control selectpicker')
            ->setHtmlAttribute('title', 'Zvolte skupiny');

        $form->addText('testTerm', 'Období *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte období ve školním roce.');

        $form->addText('schoolYear', 'Školní rok *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'rrrr/rr(rr) nebo rrrr-rr(rr)');

        $form->addInteger('testNumber', 'Číslo testu *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte číslo testu.');

        // Úvodní text se zobrazí pod hlavičkou testu
        $form->addTextArea('introductionText', 'Úvodní text')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte úvodní text testu.');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        bdump('HANDLE FORM VALIDATE');
        $values = $form->getValues();
        bdump($values);
        $validateFields['logo'] = new ValidatorArgument($values->logo, 'notEmpty');
        $validateFields['groups'] = new ValidatorArgument($values->groups, 'arrayNotEmpty');
        $validateFields['schoolYear'] = new ValidatorArgument($values->schoolYear, 'schoolYear');
        $validateFields['testNumber'] = new ValidatorArgument($values->testNumber, 'intNotNegative');
        $validateFields['testTerm'] = new ValidatorArgument($values->testTerm, 'notEmpty');
        $this->validator->validate($form, $validateFields);
        $this->redrawErrors();
    }
}