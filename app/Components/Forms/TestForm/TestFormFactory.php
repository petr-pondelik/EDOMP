<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:40
 */

namespace App\Components\Forms\TestForm;

use App\Components\Forms\FormFactory;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\GroupRepository;
use App\Model\Persistent\Repository\LogoRepository;
use App\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository;
use App\Model\Persistent\Repository\ProblemRepository;
use App\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Model\Persistent\Repository\TestRepository;
use App\Services\FileService;
use App\Services\TestGeneratorService;
use App\Services\Validator;
use Kdyby\Doctrine\EntityManager;

/**
 * Class TestFormFactory
 * @package App\Components\Forms\TestForm
 */
class TestFormFactory extends FormFactory
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
     * @var TestGeneratorService
     */
    protected $testGeneratorService;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * TestFormFactory constructor.
     * @param Validator $validator
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
     * @param TestGeneratorService $testGeneratorService
     * @param FileService $fileService
     */
    public function __construct
    (
        Validator $validator, EntityManager $entityManager,
        TestRepository $testRepository,
        ProblemRepository $problemRepository, ProblemTemplateRepository $problemTemplateRepository, ProblemFinalRepository $problemFinalRepository,
        ProblemTypeRepository $problemTypeRepository,
        DifficultyRepository $difficultyRepository, LogoRepository $logoRepository, GroupRepository $groupRepository,
        SubCategoryRepository $subCategoryRepository,
        TestGeneratorService $testGeneratorService, FileService $fileService
    )
    {
        parent::__construct($validator);
        $this->entityManager = $entityManager;
        $this->testRepository = $testRepository;
        $this->problemRepository = $problemRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemFinalRepository = $problemFinalRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->logoRepository = $logoRepository;
        $this->groupRepository = $groupRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->testGeneratorService = $testGeneratorService;
        $this->fileService = $fileService;
    }

    /**
     * @return TestFormControl
     */
    public function create(): TestFormControl
    {
        return new TestFormControl
        (
            $this->validator, $this->entityManager,
            $this->testRepository,
            $this->problemRepository, $this->problemTemplateRepository, $this->problemFinalRepository,
            $this->problemTypeRepository, $this->difficultyRepository,
            $this->logoRepository, $this->groupRepository, $this->subCategoryRepository,
            $this->testGeneratorService, $this->fileService
        );
    }
}