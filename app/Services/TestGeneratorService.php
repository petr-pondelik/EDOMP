<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.3.19
 * Time: 9:46
 */

namespace App\Services;

use App\Exceptions\GeneratorException;
use App\Exceptions\ProblemDuplicityException;
use App\Model\Persistent\Entity\Test;
use App\Model\Persistent\Functionality\FilterFunctionality;
use App\Model\Persistent\Functionality\ProblemFinal\ProblemFinalFunctionality;
use App\Model\Persistent\Functionality\TestFunctionality;
use App\Model\Persistent\Functionality\TestVariantFunctionality;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemRepository;
use App\Model\Persistent\Repository\TestRepository;
use Nette\Application\UI\ITemplate;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

/**
 * Class TestGeneratorService
 * @package App\Services
 */
class TestGeneratorService
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
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

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
     * @var GeneratorService
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
     * @var ProblemDuplicityModel
     */
    protected $problemDuplicityModel;

    /**
     * @var array
     */
    protected $testVariantsLabels;

    /**
     * @var array
     */
    protected $problemConditionTypesId;

    /**
     * TestGeneratorService constructor.
     * @param ConstraintEntityManager $entityManager
     * @param ProblemRepository $problemRepository
     * @param TestRepository $testRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemFinalFunctionality $problemFinalFunctionality
     * @param TestFunctionality $testFunctionality
     * @param TestVariantFunctionality $testVariantFunctionality
     * @param GeneratorService $generatorService
     * @param FileService $fileService
     * @param PluginContainer $pluginContainer
     * @param ProblemDuplicityModel $problemDuplicityModel
     * @param FilterFunctionality $filterFunctionality
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        ProblemRepository $problemRepository, TestRepository $testRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        ProblemFinalFunctionality $problemFinalFunctionality, TestFunctionality $testFunctionality, TestVariantFunctionality $testVariantFunctionality,
        GeneratorService $generatorService,
        FileService $fileService,
        PluginContainer $pluginContainer,
        ProblemDuplicityModel $problemDuplicityModel,
        FilterFunctionality $filterFunctionality
    )
    {
        $this->entityManager = $entityManager;
        $this->problemRepository = $problemRepository;
        $this->testRepository = $testRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->problemFinalFunctionality = $problemFinalFunctionality;
        $this->testFunctionality = $testFunctionality;
        $this->testVariantFunctionality = $testVariantFunctionality;
        $this->generatorService = $generatorService;
        $this->fileService = $fileService;
        $this->pluginContainer = $pluginContainer;
        $this->problemDuplicityModel = $problemDuplicityModel;
        $this->filterFunctionality = $filterFunctionality;

        $this->problemConditionTypesId = $this->problemConditionTypeRepository->findPairs([], 'id');

        $this->testVariantsLabels = [
            0 => 'A',
            1 => 'B',
            2 => 'C',
            3 => 'D',
            4 => 'E',
            5 => 'F',
            6 => 'G',
            7 => 'H',
            8 => 'I'
        ];
    }

    /**
     * @param ArrayHash $data
     * @return array
     */
    protected function testVariantsToArray(ArrayHash $data): array
    {
        $variants = [];
        for ($i = 0; $i < $data->variant; $i++) {
            $variants[] = $this->testVariantsLabels[$i];
        }
        return $variants;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return array
     */
    protected function getProblemFilters(int $id, ArrayHash $data): array
    {
        $filters['is_generated'] = false;
        $filters['is_template'] = $data['is_template_' . $id];
        $filters['problem_type_id'] = $data['problem_type_id_' . $id];
        $filters['difficulty_id'] = $data['difficulty_id_' . $id];
        $filters['sub_category_id'] = $data['sub_category_id_' . $id];
        foreach ($this->problemConditionTypesId as $item) {
            $filters['condition_type_id_' . $item] = $data['condition_type_id_' . $item . '_' . $id];
        }
        return $filters;
    }

    /**
     * @param array $arr
     * @return bool
     */
    protected function hasFree(array $arr): bool
    {
        foreach ($arr as $item) {
            if ($item) {
                return true;
            }
        }
        return false;
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
     * @param Test $test
     * @param string $variantLabel
     * @param ArrayHash $data
     * @return Test
     * @throws ProblemDuplicityException
     * @throws \App\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    protected function generateTestVariant(Test $test, string $variantLabel, ArrayHash $data): Test
    {
        // Prepare TestVariant entity
        $testVariant = $this->testVariantFunctionality->create(
            ArrayHash::from([
                'variantLabel' => $variantLabel,
                'test' => $test
            ]),
            false
        );

        // Iterate every added problem
        for ($i = 0; $i < $test->getProblemsPerVariant(); $i++) {

            $problemTemplate = null;
            $selectedProblems = $data['problem_' . $i];

            // In the case of random choice
            if (!$selectedProblems) {

                // Get all problems that match the filters
                $filters = $this->getProblemFilters($i, $data);
                $problems = $this->problemRepository->findFiltered($filters);

                // Get final problems that match the filters
                $filters['is_template'] = 0;
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
                $filters = $this->getProblemFilters($i, $data);

                $filters['is_template'] = 0;

                // Get all final problem that match applied filters
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

                    // If the problem isn't template, mak used final problem
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
                    bdump($problemTemplate);
                    $problem = $this->pluginContainer->getPlugin($problemTypeKeyLabel)->constructProblemFinal($problemTemplate, $this->problemDuplicityModel->getTemplateState()->getTemplateUsed($problemTemplate));
                } catch (GeneratorException $e) {
                    throw new ProblemDuplicityException('Test nelze vygenerovat bez opakujících se úloh.');
                }

                $this->problemDuplicityModel->getTemplateState()->addUsed($problem);

            }

            // Attach current problem to the created test
            $testVariant = $this->testVariantFunctionality->attachProblem($testVariant, $problem, $data->{'newpage_' . $i});

            // Create persistent filter for currently attached problem
            $this->filterFunctionality->create(
                ArrayHash::from([
                    'data' => $this->getProblemFilters($i, $data),
                    'test' => $test,
                    'seq' => $i
                ]),
                false
            );
        }

        $test->addTestVariant($testVariant);

        return $test;
    }

    /**
     * @param ArrayHash $data
     * @return Test|null
     * @throws ProblemDuplicityException
     * @throws \App\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function generateTest(ArrayHash $data): ?Test
    {
        $variants = $this->testVariantsToArray($data);

        $test = $this->testFunctionality->create(ArrayHash::from([
            'logo' => $data->logo,
            'term' => $data->testTerm,
            'schoolYear' => $data->schoolYear,
            'testNumber' => (int)$data->testNumber,
            'groups' => $data->groups,
            'introductionText' => $data->introductionText,
            'variantsCnt' => $data->variant,
            'problemsPerVariant' => $data->problemsCnt,
        ]), false);

        if ($test) {
            foreach ($variants as $variant) {
                $test = $this->generateTestVariant($test, $variant, $data);
            }
        }

        $this->entityManager->flush();

        return $test;
    }

    /**
     * @param Test $test
     * @param ITemplate $template
     * @param ArrayHash $data
     * @return Test|null
     */
    public function regenerateTest(Test $test, ITemplate $template, ArrayHash $data): ?Test
    {
        return $test;
    }

    /**
     * @param Test $test
     * @param ITemplate $template
     */
    public function createTestData(Test $test, ITemplate $template): void
    {
        FileSystem::createDir(DATA_DIR . '/tests/' . $test->getId());
        $template->test = $test;
        foreach ($test->getTestVariants()->getValues() as $testVariant) {
            $template->testVariant = $testVariant;
            file_put_contents(DATA_DIR . '/tests/' . $test->getId() . '/variant_' . Strings::lower($testVariant->getLabel()) . '.tex', (string) $template);
        }
        $this->fileService->createTestZip($test);
    }
}