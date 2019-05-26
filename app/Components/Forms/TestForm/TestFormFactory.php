<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:40
 */

namespace App\Components\Forms\TestForm;

use App\Components\Forms\BaseFormFactory;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TermRepository;
use App\Model\Repository\TestRepository;
use App\Services\FileService;
use App\Services\TestBuilderService;
use App\Services\ValidationService;
use Kdyby\Doctrine\EntityManager;

/**
 * Class TestFormFactory
 * @package App\Components\Forms\TestForm
 */
class TestFormFactory extends BaseFormFactory
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
     * TestFormFactory constructor.
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
     */
    public function __construct
    (
        ValidationService $validationService, EntityManager $entityManager,
        TestRepository $testRepository,
        ProblemRepository $problemRepository, ProblemTemplateRepository $problemTemplateRepository, ProblemTypeRepository $problemTypeRepository,
        DifficultyRepository $difficultyRepository, LogoRepository $logoRepository, GroupRepository $groupRepository,
        SubCategoryRepository $subCategoryRepository, TermRepository $termRepository,
        TestBuilderService $testBuilderService, FileService $fileService
    )
    {
        parent::__construct($validationService);
        $this->entityManager = $entityManager;
        $this->testRepository = $testRepository;
        $this->problemRepository = $problemRepository;
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
     * @return TestFormControl
     */
    public function create(): TestFormControl
    {
        return new TestFormControl
        (
            $this->validationService, $this->entityManager,
            $this->testRepository,
            $this->problemRepository, $this->problemTemplateRepository, $this->problemTypeRepository, $this->difficultyRepository,
            $this->logoRepository, $this->groupRepository, $this->subCategoryRepository, $this->termRepository,
            $this->testBuilderService, $this->fileService
        );
    }
}