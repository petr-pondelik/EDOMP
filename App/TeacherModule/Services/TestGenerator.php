<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.3.19
 * Time: 9:46
 */

namespace App\TeacherModule\Services;

use App\TeacherModule\Exceptions\GeneratorException;
use App\TeacherModule\Exceptions\ProblemDuplicityException;
use App\TeacherModule\Model\NonPersistent\Generator\Variant;
use App\TeacherModule\Helpers\TestGeneratorHelper;
use App\CoreModule\Services\FileService;
use App\CoreModule\Model\Persistent\Entity\Test;
use App\CoreModule\Model\Persistent\Entity\TestVariant;
use App\CoreModule\Model\Persistent\Functionality\FilterFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinalFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TestFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TestVariantFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ProblemRepository;
use App\CoreModule\Model\Persistent\Repository\TestRepository;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\ITemplateFactory;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

/**
 * Class TestGenerator
 * @package App\TeacherModule\Services
 */
final class TestGenerator
{
    /**
     * @var ConstraintEntityManager
     */
    protected $entityManager;

    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var TestRepository
     */
    protected $testRepository;

    /**
     * @var ProblemFinalFunctionality
     */
    protected $problemFinalFunctionality;

    /**
     * @var TestFunctionality
     */
    protected $testFunctionality;

    /**
     * @var TestVariantFunctionality
     */
    protected $testVariantFunctionality;

    /**
     * @var FilterFunctionality
     */
    protected $filterFunctionality;

    /**
     * @var ProblemGenerator
     */
    protected $generatorService;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var PluginContainer
     */
    protected $pluginContainer;

    /**
     * @var ProblemDuplicity
     */
    protected $problemDuplicityModel;

    /**
     * @var TestGeneratorHelper
     */
    protected $testGeneratorHelper;

    /**
     * @var ITemplateFactory
     */
    protected $templateFactory;

    /**
     * TestGenerator constructor.
     * @param ConstraintEntityManager $entityManager
     * @param ProblemRepository $problemRepository
     * @param TestRepository $testRepository
     * @param ProblemFinalFunctionality $problemFinalFunctionality
     * @param TestFunctionality $testFunctionality
     * @param TestVariantFunctionality $testVariantFunctionality
     * @param ProblemGenerator $generatorService
     * @param FileService $fileService
     * @param PluginContainer $pluginContainer
     * @param ProblemDuplicity $problemDuplicityModel
     * @param FilterFunctionality $filterFunctionality
     * @param TestGeneratorHelper $testGeneratorHelper
     * @param ITemplateFactory $templateFactory
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        ProblemRepository $problemRepository,
        TestRepository $testRepository,
        ProblemFinalFunctionality $problemFinalFunctionality,
        TestFunctionality $testFunctionality,
        TestVariantFunctionality $testVariantFunctionality,
        ProblemGenerator $generatorService,
        FileService $fileService,
        PluginContainer $pluginContainer,
        ProblemDuplicity $problemDuplicityModel,
        FilterFunctionality $filterFunctionality,
        TestGeneratorHelper $testGeneratorHelper,
        ITemplateFactory $templateFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->problemRepository = $problemRepository;
        $this->testRepository = $testRepository;
        $this->problemFinalFunctionality = $problemFinalFunctionality;
        $this->testFunctionality = $testFunctionality;
        $this->testVariantFunctionality = $testVariantFunctionality;
        $this->generatorService = $generatorService;
        $this->fileService = $fileService;
        $this->pluginContainer = $pluginContainer;
        $this->problemDuplicityModel = $problemDuplicityModel;
        $this->filterFunctionality = $filterFunctionality;
        $this->testGeneratorHelper = $testGeneratorHelper;
        $this->templateFactory = $templateFactory;
    }

    /**
     * @param array $keys
     * @param array $problems
     * @return array
     */
    protected function conjunctProblems(array $keys, array $problems): array
    {
        $res = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $problems)) {
                $res[] = $problems[$key];
            }
        }
        return $res;
    }

    /**
     * @param int $seq
     * @param ArrayHash $data
     * @param Test $original
     * @return bool
     */
    protected static function isProblemToGenerate(int $seq, ArrayHash $data, Test $original = null): bool
    {
        return !$original || ($original && $data->regenerateProblem[$seq]);
    }

    /**
     * @param Test $test
     * @param Variant $variant
     * @return TestVariant
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    protected function prepareTestVariant(Test $test, Variant $variant): TestVariant
    {
        // Prepare TestVariant entity
        return $this->testVariantFunctionality->create(
            ArrayHash::from([
                'variantLabel' => $variant->getLabel(),
                'test' => $test
            ]),
            false
        );
    }

    /**
     * @param Test $test
     * @param ArrayHash $data
     * @param array $selectedProblems
     * @param int $problemSeq
     * @param Test|null $original
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \ReflectionException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    protected function createPersistentFilter(Test $test, ArrayHash $data, array $selectedProblems, int $problemSeq, Test $original = null): void
    {
        $this->filterFunctionality->create(
            [
                'selectedFilters' => $this->testGeneratorHelper->getProblemFilters($problemSeq, $data, $original),
                'selectedProblems' => $selectedProblems,
                'test' => $test,
                'seq' => $problemSeq
            ],
            false
        );
    }

    /**
     * @param Test $test
     * @param Test $original
     * @param int $seq
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \ReflectionException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    protected function recreatePersistentFilter(Test $test, Test $original, int $seq): void
    {
        $this->filterFunctionality->create(
            [
                'selectedFilters' => $original->getFilters()->getValues()[$seq]->getSelectedFilters(),
                'selectedProblems' => $original->getFilters()->getValues()[$seq]->getSelectedProblems(),
                'test' => $test,
                'seq' => $seq
            ],
            false
        );
    }

    /**
     * @param Test $test
     * @param TestVariant $testVariant
     * @param Variant $variant
     * @param ArrayHash $data
     * @param int $problemSeq
     * @param Test|null $original
     * @return TestVariant
     * @throws GeneratorException
     * @throws ProblemDuplicityException
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     * @throws \ReflectionException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    protected function generateProblemVariant
    (
        Test $test, TestVariant $testVariant, Variant $variant, ArrayHash $data, int $problemSeq, Test $original = null
    ): TestVariant
    {
        $problemTemplate = null;
        $selectedProblems = $this->testGeneratorHelper::getSelectedProblems($problemSeq, $data, $original);

        // In the case of random choice
        if (!$selectedProblems) {

            // Get all problems that match the filters
            $filters = $this->testGeneratorHelper->getProblemFilters($problemSeq, $data, $original);
            $problems = $this->problemRepository->findFiltered($filters);

            if(!$problems){
                throw new GeneratorException('V rámci ' . ($problemSeq + 1) . '. úlohy nejsou k dispozici žádné záznamy.');
            }

            // Get final problems that match the filters
            $filters['isTemplate'] = 0;
            $finals = $this->problemRepository->findFiltered($filters);

            $this->problemDuplicityModel->getFinalState()->setFree($finals);

            while (true) {

                // Generate index
                $index = $this->generatorService->generateInteger(0, count($problems) - 1);

                // Pick up the problem from problems array at generated index
                $problemKeys = array_keys($problems);
                $problem = $problems[$problemKeys[$index]];

                $this->problemDuplicityModel->checkFinalDuplicityState(count($problems), count($finals));

                // If the problems isn't template, mark used final problem
                if (!$problem->isTemplate()) {
                    if ($this->problemDuplicityModel->getFinalState()->addUsed($problem)) {
                        break;
                    }
                } else {
                    break;
                }

            }
        } // If more problems was selected, pick one of them randomly
        else if (count($selectedProblems) > 1) {

            // Get applied filters and extend in by not-template condition
            $filters = $this->testGeneratorHelper->getProblemFilters($problemSeq, $data, $original);

            $filters['isTemplate'] = 0;

            // Get all final problems that match applied filters
            $finals = $this->problemRepository->findFiltered($filters);

            // Conjunct selected problems with filtered final problems
            $selectedFinals = $this->conjunctProblems($selectedProblems, $finals);

            // Prepare bool array for selected final problems
            $this->problemDuplicityModel->getFinalState()->setFree($selectedFinals);

            while (true) {

                // Generate index
                $inx = $this->generatorService->generateInteger(0, count($selectedProblems) - 1);

                // Pick up the problem from selected problems array
                $problem = $this->problemRepository->find($selectedProblems[$inx]);

                $this->problemDuplicityModel->checkFinalDuplicityState(count($selectedProblems), count($selectedFinals));

                // If the problem isn't template, mark used final problem
                if (!$problem->isTemplate()) {
                    if ($this->problemDuplicityModel->getFinalState()->addUsed($problem)) {
                        break;
                    }
                } else {
                    break;
                }

            }

        } // If only one problem was selected, just pick it up
        else {
            $problem = $this->problemRepository->find($selectedProblems[0]);
            $this->problemDuplicityModel->getFinalState()->addUsed($problem);
        }

        // If the problem is template, it needs to be generated to it's final form
        if ($problem->isTemplate()) {

            // Store generated final problem to DB and switch problemId to it's ID
            $problemTemplate = $problem;

            $problemTypeKeyLabel = $problemTemplate->getProblemType()->getKeyLabel();

            try {
                $problem = $this->pluginContainer->getPlugin($problemTypeKeyLabel)->createFinal($problemTemplate, $this->problemDuplicityModel->getTemplateState()->getTemplateUsed($problemTemplate));
            } catch (GeneratorException $e) {
                throw new ProblemDuplicityException('Test nelze vygenerovat bez opakujících se úloh.');
            }

            $this->problemDuplicityModel->getTemplateState()->addUsed($problem);

        }

        // Attach current problem to the created test
        if ($original) {
            $testVariant = $this->testVariantFunctionality->attachProblem($testVariant, $problem, $original->isNewlineAfterProblem($problemSeq));
        } else {
            $testVariant = $this->testVariantFunctionality->attachProblem($testVariant, $problem, $data->{'newPage' . $problemSeq});
        }

        // Create persistent filter for currently attached problem
        // Create each filter only once
        if ($variant->getId() === 0) {
            $this->createPersistentFilter($test, $data, $selectedProblems, $problemSeq, $original);
        }

        return $testVariant;
    }

    /**
     * @param Test $test
     * @param Variant $variant
     * @param ArrayHash $data
     * @param Test|null $original
     * @return Test
     * @throws GeneratorException
     * @throws ProblemDuplicityException
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     * @throws \ReflectionException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    protected function generateTestVariant(Test $test, Variant $variant, ArrayHash $data, Test $original = null): Test
    {
        // Prepare TestVariant entity
        $testVariant = $this->prepareTestVariant($test, $variant);

        // Iterate every added problem
        for ($seq = 0; $seq < $test->getProblemsPerVariant(); $seq++) {
            if (self::isProblemToGenerate($seq, $data, $original)) {
                $testVariant = $this->generateProblemVariant($test, $testVariant, $variant, $data, $seq, $original);
            } else {
                $testVariant = $this->testVariantFunctionality->attachAssociationFromOriginal($testVariant, $original->getProblemFinalAssociation($variant->getId(), $seq));
                if ($variant->getId() === 0) {
                    $this->recreatePersistentFilter($test, $original, $seq);
                }
            }
        }

        $test->addTestVariant($testVariant);

        return $test;
    }

    /**
     * @param ArrayHash $data
     * @return Test|null
     * @throws GeneratorException
     * @throws ProblemDuplicityException
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     * @throws \ReflectionException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function generateTest(ArrayHash $data): ?Test
    {
        bdump('GENERATE TEST');
        $variants = $this->testGeneratorHelper::getVariants($data);
        $test = $this->testFunctionality->create($this->testGeneratorHelper::preprocessTestBasicData($data), false);

        if ($test) {
            foreach ($variants as $variant) {
                $test = $this->generateTestVariant($test, $variant, $data);
            }
        }

        $this->entityManager->flush();

        return $test;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Test|null
     * @throws GeneratorException
     * @throws ProblemDuplicityException
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     * @throws \ReflectionException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function regenerateTest(int $id, ArrayHash $data): ?Test
    {
        bdump('REGENERATE TEST');
        $original = $this->testRepository->find($id);
        $data = $this->testGeneratorHelper::preprocessTestBasicData($data, $original);
        $variants = $this->testGeneratorHelper::getVariants($data);
        $test = $this->testFunctionality->create($data, false);

        if ($test) {
            foreach ($variants as $variant) {
                $test = $this->generateTestVariant($test, $variant, $data, $original);
            }
        }

        $this->entityManager->flush();

        return null;
    }

    /**
     * @param Test $test
     */
    public function createTestData(Test $test): void
    {
        bdump('CREATE TEST DATA');
        $template = $this->templateFactory->createTemplate();
        $template->setFile($this->fileService->getUserTestTemplatePath());
        $template->test = $test;
        foreach ($test->getTestVariants()->getValues() as $testVariant) {
            $template->testVariant = $testVariant;
            $this->fileService->createTestVariantFile($testVariant, (string) $template);
        }
        $this->fileService->createTestZip($test);
    }
}